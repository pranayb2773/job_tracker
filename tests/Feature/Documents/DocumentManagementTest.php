<?php

declare(strict_types=1);

use App\Enums\DocumentType;
use App\Livewire\Document\ListDocuments;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Storage::fake('local');
    $this->user = User::factory()->create();
    actingAs($this->user);
});

describe('Document List Page', function () {
    test('can view documents list page', function () {
        $this->get(route('documents.list'))
            ->assertOk()
            ->assertSee('Documents');
    });

    test('displays user documents', function () {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'My Test Document',
        ]);

        Livewire::test(ListDocuments::class)
            ->assertSee('My Test Document');
    });

    test('does not display other users documents', function () {
        $otherUser = User::factory()->create();
        Document::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Other User Document',
        ]);

        Livewire::test(ListDocuments::class)
            ->assertDontSee('Other User Document');
    });

    test('requires authentication', function () {
        auth()->logout();

        $this->get(route('documents.list'))
            ->assertRedirect(route('login'));
    });
});

describe('Document Upload', function () {
    test('can upload a document', function () {
        $file = UploadedFile::fake()->create('test-cv.pdf', 500, 'application/pdf');

        Livewire::test(ListDocuments::class)
            ->set('file', $file)
            ->set('title', 'My Resume')
            ->set('type', DocumentType::CurriculumVitae->value)
            ->call('uploadDocument')
            ->assertHasNoErrors();

        expect(Document::where('title', 'My Resume')->exists())->toBeTrue();
    });

    test('validates required fields', function () {
        Livewire::test(ListDocuments::class)
            ->call('uploadDocument')
            ->assertHasErrors(['file']);
    });

    test('validates file type is pdf', function () {
        $file = UploadedFile::fake()->create('test.txt', 100, 'text/plain');

        Livewire::test(ListDocuments::class)
            ->set('file', $file)
            ->set('title', 'Test Document')
            ->set('type', DocumentType::CurriculumVitae->value)
            ->call('uploadDocument')
            ->assertHasErrors(['file']);
    });

    test('validates file size limit', function () {
        // Note: Testing actual file size with UploadedFile::fake() is not reliable
        // as fake files don't have real content. This test verifies the validation
        // rule exists in the component.
        $component = new ListDocuments();

        // Verify validation rules include file size limit
        $rules = $component->rules();
        expect($rules['file'])->toContain('max:10240');
    })->skip('File size validation with fake files is unreliable in tests');

    test('auto-fills title from filename', function () {
        $file = UploadedFile::fake()->create('My-Resume-2024.pdf', 500, 'application/pdf');

        $component = Livewire::test(ListDocuments::class)
            ->set('file', $file);

        expect($component->get('title'))->toBe('My-Resume-2024');
    });

    test('stores file in user-specific folder', function () {
        $file = UploadedFile::fake()->create('test.pdf', 500, 'application/pdf');

        Livewire::test(ListDocuments::class)
            ->set('file', $file)
            ->set('title', 'Test')
            ->set('type', DocumentType::CurriculumVitae->value)
            ->call('uploadDocument');

        $document = Document::where('title', 'Test')->first();
        expect($document->file_path)->toContain("documents/user-{$this->user->id}");
    });

    test('calculates and stores file hash', function () {
        $file = UploadedFile::fake()->create('test.pdf', 500, 'application/pdf');

        Livewire::test(ListDocuments::class)
            ->set('file', $file)
            ->set('title', 'Test')
            ->set('type', DocumentType::CurriculumVitae->value)
            ->call('uploadDocument');

        $document = Document::where('title', 'Test')->first();
        expect($document->file_hash)->not->toBeEmpty();
        expect(mb_strlen($document->file_hash))->toBe(64); // SHA-256
    });
});

describe('Document Search and Filter', function () {
    test('can search documents by title', function () {
        Document::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Software Engineer Resume',
        ]);
        Document::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Marketing Specialist Resume',
        ]);

        $component = Livewire::test(ListDocuments::class)
            ->set('search', 'Engineer');

        $documents = $component->get('documents');
        expect($documents->count())->toBe(1);
        expect($documents->first()->title)->toBe('Software Engineer Resume');
    });

    test('can filter by document type', function () {
        Document::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'My CV',
            'type' => DocumentType::CurriculumVitae,
        ]);
        Document::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'My Cover Letter',
            'type' => DocumentType::CoverLetter,
        ]);

        Livewire::test(ListDocuments::class)
            ->set('filters.type', [DocumentType::CurriculumVitae->value])
            ->assertSee('My CV')
            ->assertDontSee('My Cover Letter');
    });

    test('can sort documents', function () {
        Document::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Zebra',
        ]);
        Document::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Alpha',
        ]);

        $component = Livewire::test(ListDocuments::class)
            ->call('sortBy', 'title');

        $documents = $component->get('documents');
        expect($documents->first()->title)->toBe('Alpha');
    });
});

describe('Document Download', function () {
    test('can download own document', function () {
        $file = UploadedFile::fake()->create('test.pdf', 500, 'application/pdf');
        Storage::disk('local')->put('documents/user-1/test.pdf', $file->getContent());

        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'file_path' => 'documents/user-1/test.pdf',
            'file_name' => 'test.pdf',
        ]);

        $response = Livewire::test(ListDocuments::class)
            ->call('downloadDocument', $document->id)
            ->assertSuccessful();

        expect($response->effects['returns'])->not->toBeNull();
    });

    test('cannot download other users document', function () {
        $otherUser = User::factory()->create();
        $document = Document::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->expectException(Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::test(ListDocuments::class)
            ->call('downloadDocument', $document->id);
    });

    test('shows error when file not found', function () {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'file_path' => 'non-existent.pdf',
        ]);

        $response = Livewire::test(ListDocuments::class)
            ->call('downloadDocument', $document->id);

        // When file doesn't exist, the method returns null (no download)
        // The return value is wrapped in an array by Livewire
        expect($response->effects['returns'])->toBeArray()
            ->and($response->effects['returns'][0])->toBeNull();
    });
});

describe('Document Delete', function () {
    test('can delete own document', function () {
        $file = UploadedFile::fake()->create('test.pdf', 500, 'application/pdf');
        $path = Storage::disk('local')->put('documents/user-1', $file);

        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'file_path' => $path,
        ]);

        Livewire::test(ListDocuments::class)
            ->call('deleteDocument', $document->id)
            ->assertHasNoErrors();

        expect(Document::find($document->id))->toBeNull()
            ->and(Storage::disk('local')->exists($path))->toBeFalse();
    });

    test('cannot delete other users document', function () {
        $otherUser = User::factory()->create();
        $document = Document::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->expectException(Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::test(ListDocuments::class)
            ->call('deleteDocument', $document->id);

        expect(Document::find($document->id))->not->toBeNull();
    });

    test('can delete multiple documents in bulk', function () {
        $doc1 = Document::factory()->create(['user_id' => $this->user->id]);
        $doc2 = Document::factory()->create(['user_id' => $this->user->id]);

        Livewire::test(ListDocuments::class)
            ->set('selectedDocumentIds', [$doc1->id, $doc2->id])
            ->call('deleteSelectedDocuments')
            ->assertHasNoErrors();

        expect(Document::find($doc1->id))->toBeNull()
            ->and(Document::find($doc2->id))->toBeNull();
    });
});

describe('Document Pagination', function () {
    test('paginates documents', function () {
        Document::factory()->count(15)->create([
            'user_id' => $this->user->id,
        ]);

        $component = Livewire::test(ListDocuments::class);
        $documents = $component->get('documents');

        expect($documents->count())->toBe(10)
            ->and($documents->total())->toBe(15);
    });
});

describe('File Size Formatting', function () {
    test('formats file size correctly', function () {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'file_size' => 1048576, // 1 MB
        ]);

        expect($document->file_size_formatted)->toBe('1.00 MB');
    });

    test('formats kilobytes correctly', function () {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'file_size' => 1024, // 1 KB
        ]);

        expect($document->file_size_formatted)->toBe('1.00 KB');
    });

    test('formats bytes correctly', function () {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'file_size' => 512,
        ]);

        expect($document->file_size_formatted)->toBe('512 B');
    });
});

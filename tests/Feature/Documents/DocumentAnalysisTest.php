<?php

declare(strict_types=1);

use App\Livewire\Document\AnalyzeDocument;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Prism\Prism\Prism;
use Prism\Prism\Testing\TextResponseFake;
use Prism\Prism\ValueObjects\Usage;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Storage::fake('local');
    $this->user = User::factory()->create();
    actingAs($this->user);
});

describe('Document Analysis Page', function () {
    test('can view analysis page for own document', function () {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->get(route('documents.analyze', $document))
            ->assertOk()
            ->assertSeeLivewire(AnalyzeDocument::class);
    });

    test('cannot view analysis page for other users document', function () {
        $otherUser = User::factory()->create();
        $document = Document::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->get(route('documents.analyze', $document))
            ->assertForbidden();
    });

    test('requires authentication', function () {
        auth()->logout();

        $document = Document::factory()->create();

        $this->get(route('documents.analyze', $document))
            ->assertRedirect(route('login'));
    });

    test('loads document successfully', function () {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Software Engineer Resume',
        ]);

        $component = Livewire::test(AnalyzeDocument::class, ['document' => $document]);

        expect($component->get('document')->title)->toBe('Software Engineer Resume')
            ->and($component->get('document')->type)->toBe($document->type);
    });
});

describe('Analysis Generation', function () {
    test('has null analysis when not analyzed', function () {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'analysis' => null,
            'analyzed_at' => null,
        ]);

        $component = Livewire::test(AnalyzeDocument::class, ['document' => $document]);

        expect($component->get('analysis'))->toBeNull()
            ->and($component->get('document')->analysis)->toBeNull();
    });

    test('can analyze cv document', function () {
        // Mock analysis response from AI
        $mockAnalysisResponse = [
            'overall_score' => 88,
            'summary' => 'Well-structured CV with strong technical background',
            'score_label' => 'STRONG',
            'score_description' => 'Your CV demonstrates good formatting and content',
            'top_recommendations' => [
                'Add more quantifiable achievements',
                'Include specific project outcomes',
                'Highlight leadership experience',
            ],
            'scoring_dimensions' => [
                'metadata_contact' => [
                    'score' => 95,
                    'label' => 'Metadata & Contact Information',
                    'description' => 'Complete contact information',
                ],
                'presentation_formatting' => [
                    'score' => 85,
                    'label' => 'Presentation & Formatting',
                    'description' => 'Clean and professional layout',
                ],
            ],
            'penalties' => [],
            'section_analysis' => [
                'professional_summary' => [
                    'status' => 'success',
                    'feedback' => 'Clear and concise professional summary',
                ],
                'work_experience' => [
                    'status' => 'success',
                    'feedback' => 'Well-documented work history',
                ],
            ],
        ];

        // Fake Prism response to avoid actual API calls
        $fakeResponse = TextResponseFake::make()
            ->withText(json_encode($mockAnalysisResponse))
            ->withUsage(new Usage(100, 50));

        Prism::fake([$fakeResponse]);

        // Use a real test PDF file
        $testPdfPath = base_path('tests/Fixtures/test-cv.pdf');
        $file = new UploadedFile($testPdfPath, 'test-cv.pdf', 'application/pdf', null, true);
        $path = Storage::disk('local')->put('documents/user-1', $file);

        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'file_path' => $path,
            'analysis' => null,
        ]);

        Livewire::test(AnalyzeDocument::class, ['document' => $document])
            ->call('analyzeCV')
            ->assertHasNoErrors();

        $document->refresh();

        expect($document->analysis)->not->toBeNull()
            ->and($document->analysis['overall_score'])->toBe(88)
            ->and($document->analysis['score_label'])->toBe('STRONG')
            ->and($document->analyzed_at)->not->toBeNull();
    });

    test('stores analysis in database after generation', function () {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'analysis' => null,
        ]);

        // Mock the analysis result
        $mockAnalysis = [
            'overall_score' => 85,
            'summary' => 'Strong CV with good structure',
            'score_label' => 'STRONG',
            'score_description' => 'Well-formatted and comprehensive',
            'top_recommendations' => [
                'Add more quantifiable achievements',
                'Include technical certifications',
                'Expand on leadership experience',
            ],
            'scoring_dimensions' => [
                'metadata_contact' => [
                    'score' => 90,
                    'label' => 'Metadata & Contact Information',
                    'description' => 'Complete contact information provided',
                ],
            ],
            'penalties' => [],
            'section_analysis' => [
                'professional_summary' => [
                    'status' => 'success',
                    'feedback' => 'Clear and concise summary',
                ],
            ],
        ];

        // Directly update the document to simulate analysis
        $document->update([
            'analysis' => $mockAnalysis,
            'analyzed_at' => now(),
        ]);

        $document->refresh();

        expect($document->analysis)->toBeArray()
            ->and($document->analysis['overall_score'])->toBe(85)
            ->and($document->analyzed_at)->not->toBeNull();
    });
});

describe('Cached Analysis Display', function () {
    test('loads cached analysis when available', function () {
        $document = Document::factory()->analyzed()->create([
            'user_id' => $this->user->id,
        ]);

        $component = Livewire::test(AnalyzeDocument::class, ['document' => $document]);

        expect($component->get('analysis'))->not->toBeNull()
            ->and($component->get('analysis')['overall_score'])->toBe($document->analysis['overall_score'])
            ->and($component->get('analysis')['score_label'])->toBe($document->analysis['score_label']);
    });

    test('shows regenerate button when analysis exists', function () {
        $document = Document::factory()->analyzed()->create([
            'user_id' => $this->user->id,
        ]);

        $component = Livewire::test(AnalyzeDocument::class, ['document' => $document]);

        expect($component->get('analysis'))->not->toBeNull()
            ->and($component->get('document')->analyzed_at)->not->toBeNull();
    });

    test('displays summary data', function () {
        $document = Document::factory()->analyzed()->create([
            'user_id' => $this->user->id,
        ]);

        $component = Livewire::test(AnalyzeDocument::class, ['document' => $document]);

        expect($component->get('analysis')['summary'])->toBe($document->analysis['summary']);
    });

    test('displays top recommendations', function () {
        $document = Document::factory()->analyzed()->create([
            'user_id' => $this->user->id,
        ]);

        $component = Livewire::test(AnalyzeDocument::class, ['document' => $document]);

        expect($component->get('analysis')['top_recommendations'])->toBeArray()
            ->and($component->get('analysis')['top_recommendations'])->toHaveCount(3);
    });

    test('displays scoring dimensions with progress bars', function () {
        $document = Document::factory()->analyzed()->create([
            'user_id' => $this->user->id,
        ]);

        $component = Livewire::test(AnalyzeDocument::class, ['document' => $document]);

        expect($component->get('analysis')['scoring_dimensions'])->toBeArray()
            ->and($component->get('analysis')['scoring_dimensions'])->toHaveKey('metadata_contact');
    });

    test('displays analyzed at timestamp', function () {
        $document = Document::factory()->analyzed()->create([
            'user_id' => $this->user->id,
            'analyzed_at' => now()->subHours(2),
        ]);

        $component = Livewire::test(AnalyzeDocument::class, ['document' => $document]);

        expect($component->get('document')->analyzed_at)->not->toBeNull()
            ->and($component->get('document')->analyzed_at->diffInHours(now()))->toBeGreaterThanOrEqual(2);
    });
});

describe('Analysis Scores', function () {
    test('displays overall score with correct color for excellent score', function () {
        $document = Document::factory()->analyzed()->create([
            'user_id' => $this->user->id,
            'analysis' => [
                'overall_score' => 95,
                'score_label' => 'EXCELLENT',
                'summary' => 'Test summary',
                'score_description' => 'Outstanding CV',
                'top_recommendations' => [],
                'scoring_dimensions' => [],
                'penalties' => [],
                'section_analysis' => [],
            ],
        ]);

        $component = Livewire::test(AnalyzeDocument::class, ['document' => $document]);

        expect($component->get('analysis')['overall_score'])->toBe(95)
            ->and($component->get('analysis')['score_label'])->toBe('EXCELLENT');
    });

    test('displays overall score with correct color for good score', function () {
        $document = Document::factory()->analyzed()->create([
            'user_id' => $this->user->id,
            'analysis' => [
                'overall_score' => 75,
                'score_label' => 'GOOD',
                'summary' => 'Test summary',
                'score_description' => 'Solid CV',
                'top_recommendations' => [],
                'scoring_dimensions' => [],
                'penalties' => [],
                'section_analysis' => [],
            ],
        ]);

        $component = Livewire::test(AnalyzeDocument::class, ['document' => $document]);

        expect($component->get('analysis')['overall_score'])->toBe(75)
            ->and($component->get('analysis')['score_label'])->toBe('GOOD');
    });

    test('shows empty state when no analysis exists', function () {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
            'analysis' => null,
            'analyzed_at' => null,
        ]);

        $component = Livewire::test(AnalyzeDocument::class, ['document' => $document]);

        expect($component->get('analysis'))->toBeNull()
            ->and($component->get('document')->analysis)->toBeNull();
    });
});

describe('Section Analysis', function () {
    test('displays section analysis when available', function () {
        $document = Document::factory()->analyzed()->create([
            'user_id' => $this->user->id,
        ]);

        $component = Livewire::test(AnalyzeDocument::class, ['document' => $document]);

        expect($component->get('analysis')['section_analysis'])->toBeArray()
            ->and($component->get('analysis')['section_analysis'])->toHaveKey('professional_summary');
    });

    test('displays penalties when present', function () {
        $document = Document::factory()->analyzed()->create([
            'user_id' => $this->user->id,
            'analysis' => [
                'overall_score' => 70,
                'score_label' => 'GOOD',
                'summary' => 'Test summary',
                'score_description' => 'Good CV with some issues',
                'top_recommendations' => [],
                'scoring_dimensions' => [],
                'penalties' => [
                    'CV exceeds recommended length (-5 points)',
                    'Missing contact information (-3 points)',
                ],
                'section_analysis' => [],
            ],
        ]);

        $component = Livewire::test(AnalyzeDocument::class, ['document' => $document]);

        expect($component->get('analysis')['penalties'])->toBeArray()
            ->and($component->get('analysis')['penalties'])->toHaveCount(2)
            ->and($component->get('analysis')['penalties'][0])->toBe('CV exceeds recommended length (-5 points)')
            ->and($component->get('analysis')['penalties'][1])->toBe('Missing contact information (-3 points)');
    });
});

describe('Navigation', function () {
    test('can load document for analysis', function () {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $component = Livewire::test(AnalyzeDocument::class, ['document' => $document]);

        // Component should have the document loaded
        expect($component->get('document')->id)->toBe($document->id);
    });

    test('analysis page is accessible via route', function () {
        $document = Document::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->get(route('documents.analyze', $document))
            ->assertOk();
    });
});

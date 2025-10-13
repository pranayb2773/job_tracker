<?php

declare(strict_types=1);

namespace App\Livewire\Document;

use App\Models\Document;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @property LengthAwarePaginator|Document[] $documents
 */
final class ListDocuments extends Component
{
    use WithFileUploads;
    use WithPagination;

    public array $selectedDocumentIds = [];

    public array $documentIdsOnPage = [];

    public array $documentIds = [];

    public array $filters = [];

    public int $activeFilterCount = 0;

    public string $search = '';

    public string $sortCol = '';

    public string $sortDirection = 'asc';

    public string $title = '';

    #[Validate('required|string')]
    public string $type = '';

    #[Validate('required|file|max:1024|mimes:pdf')]
    public $file = null;

    public function render(): View
    {
        $this->documentIds = $this->allDocumentIds();
        $this->documentIdsOnPage = $this->documents->map(fn (Document $document) => (string) $document->id)->toArray();

        return view('livewire.document.list-documents')
            ->title(config('app.name').' | List Documents');
    }

    public function updated($property): void
    {
        if (str($property)->contains('filters')) {
            $this->activeFilterCount = count(array_filter($this->filters));
            $this->reset(['selectedDocumentIds', 'documentIdsOnPage']);
            $this->resetPage();
        }

        if ($property === 'search') {
            $this->reset(['selectedDocumentIds', 'documentIdsOnPage']);
            $this->resetPage();
        }
    }

    #[Computed]
    public function documents(): LengthAwarePaginator
    {
        $query = Document::query()->whereBelongsTo(Auth::user());
        $this->applySearch($query);
        $this->applyFilters($query);
        $this->applySorting($query);

        return $query->paginate(10);
    }

    public function sortBy(string $column): void
    {
        if ($this->sortCol === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortCol = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function applySearch(Builder $query): void
    {
        $query->when($this->search, function (Builder $query): void {
            $query->whereAny(['title'], 'like', "%{$this->search}%");
        });
    }

    public function applyFilters(Builder $query): void
    {
        $query->when($this->filters['type'] ?? null, function (Builder $query): void {
            $query->whereIn('type', $this->filters['type']);
        });
    }

    public function applySorting(Builder $query): void
    {
        match ($this->sortCol) {
            'title', 'type' => $query->orderBy($this->sortCol, $this->sortDirection),
            default => $query->orderBy('updated_at', $this->sortDirection),
        };
    }

    public function downloadDocument(int $documentId): ?StreamedResponse
    {
        $document = Document::query()
            ->where('id', $documentId)
            ->whereBelongsTo(Auth::user())
            ->firstOrFail();

        if (! Storage::disk('local')->exists($document->file_path)) {
            Flux::toast(
                text: 'File not found.',
                heading: 'Download Failed',
                variant: 'danger',
            );

            return null;
        }

        return Storage::disk('local')->download(
            $document->file_path,
            $document->file_name
        );
    }

    public function deleteDocument(int $documentId): void
    {
        $document = Document::query()
            ->where('id', $documentId)
            ->whereBelongsTo(Auth::user())
            ->firstOrFail();

        // Delete the physical file
        if (Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }

        $document->delete();

        $this->reset('selectedDocumentIds');

        Flux::toast(
            text: "Document {$document->title} successfully deleted.",
            heading: 'Document Deleted',
            variant: 'success',
        );
    }

    public function deleteSelectedDocuments(): void
    {
        $documents = Document::query()
            ->whereIn('id', $this->selectedDocumentIds)
            ->whereBelongsTo(Auth::user())
            ->get();

        // Delete the physical files
        foreach ($documents as $document) {
            if (Storage::disk('local')->exists($document->file_path)) {
                Storage::disk('local')->delete($document->file_path);
            }
        }

        // Delete the database records
        $documents->each->delete();

        $this->reset('selectedDocumentIds');
        Flux::modal('delete-documents-bulk')->close();

        Flux::toast(
            text: 'Selected documents successfully deleted.',
            heading: 'Documents Deleted',
            variant: 'success',
        );
    }

    public function updatedFile(): void
    {
        if ($this->file) {
            $originalName = $this->file->getClientOriginalName();
            $this->title = pathinfo($originalName, PATHINFO_FILENAME);
        }
    }

    public function uploadDocument(): void
    {
        $this->validate([
            'type' => 'required|string',
            'file' => 'required|file|max:10240|mimes:pdf',
        ]);

        if (empty($this->title)) {
            $this->title = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
        }

        $userId = Auth::id();
        $filePath = $this->file->store("documents/user-{$userId}", 'local');
        $fileHash = hash_file('sha256', $this->file->getRealPath());

        Document::query()->create([
            'user_id' => $userId,
            'title' => $this->title,
            'type' => $this->type,
            'file_name' => $this->file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_mime_type' => $this->file->getMimeType(),
            'file_size' => $this->file->getSize(),
            'file_hash' => $fileHash,
        ]);

        $this->reset(['title', 'type', 'file']);
        Flux::modal('upload-document')->close();

        Flux::toast(
            text: 'Document uploaded successfully.',
            heading: 'Document Uploaded',
            variant: 'success',
        );
    }

    protected function allDocumentIds(): array
    {
        return Document::query()
            ->whereBelongsTo(Auth::user())
            ->when($this->search, fn (Builder $q) => $this->applySearch($q))
            ->when($this->filters, fn (Builder $q) => $this->applyFilters($q))
            ->pluck('id')
            ->map(fn (int $id) => (string) $id)
            ->toArray();
    }
}

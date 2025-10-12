<?php

declare(strict_types=1);

namespace App\Livewire\Document;

use App\Models\Document;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property LengthAwarePaginator|Document[] $documents
 */
final class ListDocuments extends Component
{
    use WithPagination;

    public array $selectedDocumentIds = [];

    public array $documentIdsOnPage = [];

    public array $documentIds = [];

    public array $filters = [];
    public int $activeFilterCount = 0;

    public string $search = '';

    public string $sortCol = '';

    public string $sortDirection = 'asc';

    public function render(): View
    {
        $this->documentIds = $this->allDocumentIds();
        $this->documentIdsOnPage = $this->documents->map(fn(Document $document) => (string) $document->id)->toArray();

        return view('livewire.document.list-documents')
            ->title(config('app.name') . ' | List Documents');
    }

    #[Computed]
    public function documents(): LengthAwarePaginator
    {
        $query = Document::query();
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

    public function deleteDocument(int $documentId): void
    {
        $document = Document::query()
            ->where('id', $documentId)
            ->whereBelongsTo(Auth::user())
            ->firstOrFail();

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
        Document::query()
            ->whereIn('id', $this->selectedDocumentIds)
            ->whereBelongsTo(Auth::user())
            ->delete();

        $this->reset('selectedDocumentIds');
        Flux::modal('delete-documents-bulk')->close();

        Flux::toast(
            text: 'Selected documents successfully deleted.',
            heading: 'Documents Deleted',
            variant: 'success',
        );
    }

    protected function allDocumentIds(): array
    {
        return Document::query()
            ->whereBelongsTo(Auth::user())
            ->when($this->search, fn(Builder $q) => $this->applySearch($q))
            ->when($this->filters, fn(Builder $q) => $this->applyFilters($q))
            ->pluck('id')
            ->map(fn(int $id) => (string) $id)
            ->toArray();
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\Application;

use App\Models\JobApplication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property LengthAwarePaginator|JobApplication[] $applications
 */
final class ListJobApplications extends Component
{
    use WithPagination;

    public array $selectedApplicationIds = [];

    public array $applicationIdsOnPage = [];

    public array $applicationIds = [];

    public array $filters = [];

    public int $activeFilterCount = 0;

    public string $search = '';

    public string $sortCol = '';

    public string $sortDirection = 'asc';

    public function render(): View
    {
        $this->applicationIds = $this->allApplicationIds();
        $this->applicationIdsOnPage = $this->applications->map(fn(JobApplication $application) => (string)$application->id)->toArray();

        return view('livewire.application.list-job-applications')
            ->title(config('app.name') . ' | List Applications');
    }

    public function updated(string $property): void
    {
        if (str($property)->contains('filters')) {
            $this->activeFilterCount = count(array_filter($this->filters));
            $this->reset(['selectedApplicationIds', 'applicationIdsOnPage']);
            $this->resetPage();
        }

        if ($property === 'search') {
            $this->reset(['selectedApplicationIds', 'applicationIdsOnPage']);
            $this->resetPage();
        }
    }

    #[Computed]
    public function applications(): LengthAwarePaginator
    {
        $query = JobApplication::query()->whereBelongsTo(Auth::user());
        $this->applySearch($query);
        $this->applyFilters($query);
        $this->applySorting($query);

        return $query->paginate(10);
    }

    public function applySearch(Builder $query): void
    {
        $query->when($this->search, function (Builder $query): void {
            $query->whereAny(['job_title', 'organisation'], 'like', "%{$this->search}%");
        });
    }

    public function applyFilters(Builder $query): void
    {
        $query->when($this->filters['status'] ?? null, function (Builder $query): void {
            $query->whereIn('status', $this->filters['status']);
        });

        $query->when($this->filters['priority'] ?? null, function (Builder $query): void {
            $query->where('priority', $this->filters['priority']);
        });

        $query->when($this->filters['application_date'] ?? null, function (Builder $query): void {
            $query->whereDate('application_date', $this->filters['application_date']);
        });
    }

    public function applySorting(Builder $query): void
    {
        match ($this->sortCol) {
            'title' => $query->orderBy('job_title', $this->sortDirection),
            'organisation', 'status', 'priority' => $query->orderBy($this->sortCol, $this->sortDirection),
            default => $query->orderBy('application_date', $this->sortDirection),
        };
    }

    protected function allApplicationIds(): array
    {
        return JobApplication::query()
            ->whereBelongsTo(Auth::user())
            ->when($this->search, fn(Builder $q) => $this->applySearch($q))
            ->when($this->filters, fn(Builder $q) => $this->applyFilters($q))
            ->pluck('id')
            ->map(fn(int $id) => (string)$id)
            ->toArray();
    }
}

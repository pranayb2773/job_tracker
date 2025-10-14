<?php

declare(strict_types=1);

namespace App\Livewire\Application;

use App\Models\JobApplication;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
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

    public string $sortDirection = '';

    public function render(): View
    {
        return view('livewire.application.list-job-applications')
            ->title(config('app.name').' | List Applications');
    }
}

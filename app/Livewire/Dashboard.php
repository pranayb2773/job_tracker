<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ApplicationStatus;
use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\JobApplication;
use App\Services\AI\RateLimiting\AnalysisRateLimiter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

final class Dashboard extends Component
{
    public function render(): View
    {
        return view('livewire.dashboard', [
            'applicationsByStatus' => $this->getApplicationsByStatus(),
            'documentsByType' => $this->getDocumentsByType(),
            'remainingAnalyses' => $this->getRemainingAnalyses(),
            'latestApplications' => $this->getLatestApplications(),
        ])->title(config('app.name').' | Dashboard');
    }

    /**
     * Get count of applications grouped by status.
     *
     * @return Collection<string, int>
     */
    private function getApplicationsByStatus(): Collection
    {
        $counts = JobApplication::query()
            ->whereBelongsTo(Auth::user())
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Ensure all statuses are represented, even with 0 count
        return collect(ApplicationStatus::cases())
            ->mapWithKeys(function (ApplicationStatus $status) use ($counts): array {
                return [$status->value => $counts->get($status->value, 0)];
            });
    }

    /**
     * Get count of documents grouped by type.
     *
     * @return Collection<string, int>
     */
    private function getDocumentsByType(): Collection
    {
        $counts = Document::query()
            ->whereBelongsTo(Auth::user())
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        // Ensure all document types are represented, even with 0 count
        return collect(DocumentType::cases())
            ->mapWithKeys(function (DocumentType $type) use ($counts): array {
                return [$type->value => $counts->get($type->value, 0)];
            });
    }

    /**
     * Get remaining CV analyses for today.
     */
    private function getRemainingAnalyses(): int
    {
        $rateLimiter = app(AnalysisRateLimiter::class);

        return $rateLimiter->remaining(Auth::user(), 'cv_analysis');
    }

    /**
     * Get the latest 5 job applications.
     *
     * @return Collection<int, JobApplication>
     */
    private function getLatestApplications(): Collection
    {
        return JobApplication::query()
            ->whereBelongsTo(Auth::user())
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
    }
}

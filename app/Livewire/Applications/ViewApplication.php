<?php

declare(strict_types=1);

namespace App\Livewire\Applications;

use App\Exceptions\AnalysisRateLimitException;
use App\Models\JobApplication;
use App\Services\RoleAnalysis\RoleAnalysisService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class ViewApplication extends Component
{
    public JobApplication $application;

    public ?array $roleAnalysis = null;

    public bool $isAnalyzingRole = false;

    public int $remainingAnalyses = 0;

    public function mount(JobApplication $application): void
    {
        // Ensure the user owns the application
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $this->application = $application->load('documents');
        $this->roleAnalysis = $this->application->role_analysis ?? null;

        // Initialize remaining role analyses
        $service = app(RoleAnalysisService::class);
        $this->remainingAnalyses = $service->getRemainingAnalyses(Auth::user());
    }

    public function render(): View
    {
        return view('livewire.applications.view-application')
            ->title(config('app.name').' | '.$this->application->job_title);
    }

    public function analyzeRole(RoleAnalysisService $service): void
    {
        $desc = (string) ($this->application->job_description ?? '');
        if (mb_strlen(strip_tags($desc)) < 100) {
            Flux::toast(
                text: 'Please add a longer job description (min 100 chars).',
                heading: 'Job Description Required',
                variant: 'warning',
            );

            return;
        }

        $this->isAnalyzingRole = true;
        try {
            set_time_limit(120);
            $result = $service->analyze($desc, Auth::user());
            $this->roleAnalysis = $result->data;

            // Persist to application JSON column
            $this->application->role_analysis = $this->roleAnalysis;
            $this->application->save();

            $this->remainingAnalyses = $service->getRemainingAnalyses(Auth::user());

            Flux::toast(
                text: 'Role analysis completed successfully.',
                heading: 'Analysis Complete',
                variant: 'success',
            );
        } catch (AnalysisRateLimitException $e) {
            Flux::toast(
                text: $e->getMessage(),
                heading: 'Daily Limit Reached',
                variant: 'warning',
            );
        } catch (Throwable $e) {
            Flux::toast(
                text: 'An error occurred while analyzing the role. Please try again.',
                heading: 'Analysis Failed',
                variant: 'danger',
            );
            logger()->error('Role analysis failed', [
                'user_id' => Auth::id(),
                'application_id' => $this->application->id,
                'error' => $e->getMessage(),
            ]);
        } finally {
            $this->isAnalyzingRole = false;
        }
    }

    public function downloadDocument(int $documentId): ?StreamedResponse
    {
        $document = $this->application
            ->whereBelongsTo(Auth::user())
            ->documents()
            ->where('documents.id', $documentId)
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
}

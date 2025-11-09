<?php

declare(strict_types=1);

namespace App\Livewire\AI;

use App\Exceptions\AnalysisRateLimitException;
use App\Services\RoleAnalysis\RoleAnalysisService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

final class RoleAnalysis extends Component
{
    public string $jobDescription = '';

    public ?array $analysis = null;

    public bool $isAnalyzing = false;

    public int $remainingAnalyses = 0;

    public function mount(): void
    {
        $service = app(RoleAnalysisService::class);
        $this->remainingAnalyses = $service->getRemainingAnalyses(Auth::user());
    }

    public function analyzeRole(RoleAnalysisService $service): void
    {
        $this->validate([
            'jobDescription' => ['required', 'string', 'min:100'],
        ], [
            'jobDescription.required' => 'Please paste a job description to analyze.',
            'jobDescription.min' => 'Job description must be at least 100 characters for meaningful analysis.',
        ]);

        $this->isAnalyzing = true;

        try {
            $result = $service->analyze($this->jobDescription, Auth::user());
            $this->analysis = $result->data;

            $this->remainingAnalyses = $service->getRemainingAnalyses(Auth::user());

            Flux::toast(
                text: "Role analysis completed successfully. You have {$this->remainingAnalyses} analyses remaining today.",
                heading: 'Analysis Complete',
                variant: 'success',
            );
        } catch (AnalysisRateLimitException $e) {
            Flux::toast(
                text: $e->getMessage(),
                heading: 'Daily Limit Reached',
                variant: 'warning',
            );
        } catch (\Throwable $e) {
            Flux::toast(
                text: 'An error occurred while analyzing the role. Please try again.',
                heading: 'Analysis Failed',
                variant: 'danger',
            );

            logger()->error('Role analysis failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
        } finally {
            $this->isAnalyzing = false;
        }
    }

    public function clearAnalysis(): void
    {
        $this->reset('analysis', 'jobDescription');
    }

    public function render(): View
    {
        return view('livewire.ai.role-analysis')
            ->title(config('app.name') . ' | AI Role Analysis');
    }
}

<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\JobApplication;
use App\Services\RoleAnalysis\RoleAnalysisService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

final class ProcessRoleAnalysis implements ShouldQueue
{
    use Queueable;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 360;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public JobApplication $application,
        public string $jobDescription
    ) {}

    /**
     * Execute the job.
     */
    public function handle(RoleAnalysisService $service): void
    {
        try {
            Log::info('Starting role analysis job', [
                'application_id' => $this->application->id,
                'user_id' => $this->application->user_id,
            ]);

            // Perform the analysis
            $result = $service->analyze($this->jobDescription, $this->application->user);

            // Update the application with analysis results
            $this->application->update([
                'role_analysis' => $result->data,
            ]);

            Log::info('Role analysis completed successfully', [
                'application_id' => $this->application->id,
                'provider' => $result->provider,
                'model' => $result->model,
            ]);
        } catch (Exception $e) {
            Log::error('Role analysis job failed', [
                'application_id' => $this->application->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Exception $exception): void
    {
        Log::error('Role analysis job failed permanently', [
            'application_id' => $this->application->id,
            'error' => $exception?->getMessage(),
        ]);
    }
}

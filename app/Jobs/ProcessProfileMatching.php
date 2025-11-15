<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Document;
use App\Models\JobApplication;
use App\Services\AI\Contracts\AIProviderInterface;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

final class ProcessProfileMatching implements ShouldQueue
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
        public Document $cvDocument,
        public string $jobDescription,
        public string $systemPrompt
    ) {}

    /**
     * Execute the job.
     */
    public function handle(AIProviderInterface $provider): void
    {
        try {
            Log::info('Starting profile matching job', [
                'application_id' => $this->application->id,
                'cv_document_id' => $this->cvDocument->id,
                'user_id' => $this->application->user_id,
            ]);

            // Perform the profile matching analysis
            $result = $provider->analyzeProfileMatching(
                cvDocument: $this->cvDocument,
                jobDescription: $this->jobDescription,
                jobTitle: $this->application->job_title,
                organisation: $this->application->organisation,
                systemPrompt: $this->systemPrompt
            );

            // Update the application with profile matching results
            $this->application->update([
                'profile_matching' => $result->data,
            ]);

            Log::info('Profile matching completed successfully', [
                'application_id' => $this->application->id,
                'provider' => $result->provider,
                'model' => $result->model,
            ]);
        } catch (Exception $e) {
            Log::error('Profile matching job failed', [
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
        Log::error('Profile matching job failed permanently', [
            'application_id' => $this->application->id,
            'error' => $exception?->getMessage(),
        ]);
    }
}

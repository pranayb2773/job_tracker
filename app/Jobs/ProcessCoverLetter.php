<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\JobApplication;
use App\Services\AI\Contracts\AIProviderInterface;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

final class ProcessCoverLetter implements ShouldQueue
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
        public string $input,
        public string $systemPrompt
    ) {}

    /**
     * Execute the job.
     */
    public function handle(AIProviderInterface $provider): void
    {
        try {
            Log::info('Starting cover letter generation job', [
                'application_id' => $this->application->id,
                'user_id' => $this->application->user_id,
            ]);

            // Generate cover letter using AI provider
            $response = $provider->analyzeText($this->input, $this->systemPrompt);

            // Parse the response (could be JSON or plain text)
            $payload = null;
            $decoded = json_decode($response, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $content = isset($decoded['content'])
                    ? mb_trim((string)$decoded['content'])
                    : mb_trim($response);
                $payload = $decoded;
                $payload['generated_at'] = now()->toIso8601String();
            } else {
                $content = mb_trim($response);
                $payload = [
                    'content' => $content,
                    'generated_at' => now()->toIso8601String(),
                ];
            }

            // Update the application with cover letter
            $this->application->update([
                'cover_letter' => $payload,
            ]);

            Log::info('Cover letter generation completed successfully', [
                'application_id' => $this->application->id,
            ]);
        } catch (Exception $e) {
            Log::error('Cover letter generation job failed', [
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
        Log::error('Cover letter generation job failed permanently', [
            'application_id' => $this->application->id,
            'error' => $exception?->getMessage(),
        ]);
    }
}

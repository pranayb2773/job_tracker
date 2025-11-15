<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Document;
use App\Services\CVAnalysis\CVAnalysisService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

final class ProcessCVAnalysis implements ShouldQueue
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
        public Document $document
    ) {}

    /**
     * Execute the job.
     */
    public function handle(CVAnalysisService $service): void
    {
        try {
            Log::info('Starting CV analysis job', [
                'document_id' => $this->document->id,
                'user_id' => $this->document->user_id,
            ]);

            // Perform the analysis
            $result = $service->analyze($this->document);

            // Update the document with analysis results
            $this->document->update([
                'analysis' => $result->data,
                'analyzed_at' => now(),
            ]);

            Log::info('CV analysis completed successfully', [
                'document_id' => $this->document->id,
                'provider' => $result->provider,
                'model' => $result->model,
            ]);
        } catch (Exception $e) {
            Log::error('CV analysis job failed', [
                'document_id' => $this->document->id,
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
        Log::error('CV analysis job failed permanently', [
            'document_id' => $this->document->id,
            'error' => $exception?->getMessage(),
        ]);

        // Optionally notify the user or mark the document as failed
        $this->document->update([
            'analysis_failed_at' => now(),
        ]);
    }
}

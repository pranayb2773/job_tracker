<?php

declare(strict_types=1);

namespace App\Services\CVAnalysis;

use App\Exceptions\AnalysisRateLimitException;
use App\Models\Document;
use App\Services\CVAnalysis\Contracts\AIProviderInterface;
use App\Services\CVAnalysis\DTOs\AnalysisResult;
use App\Services\CVAnalysis\RateLimiting\AnalysisRateLimiter;
use Exception;

final readonly class CVAnalysisService
{
    public function __construct(
        private AIProviderInterface $provider,
        private AnalysisRateLimiter $rateLimiter
    )
    {
    }

    /**
     * Analyze a CV/Resume document using the configured AI provider.
     *
     * @throws Exception
     * @throws AnalysisRateLimitException
     */
    public function analyze(Document $document): AnalysisResult
    {
        // Check rate limit before proceeding
        $this->rateLimiter->check($document->user);

        // Increase PHP execution time for PDF processing
        set_time_limit(300); // 5 minutes

        // Load system prompt
        $systemPrompt = file_get_contents(resource_path('prompts/cv-analysis.md'));

        // Get previous analysis if this is a regeneration
        $previousAnalysis = $document->analysis && $document->analyzed_at
            ? $document->analysis
            : null;

        // Delegate to the AI provider
        $result = $this->provider->analyze($document, $systemPrompt, $previousAnalysis);

        // Record the analysis attempt (decrement available count)
        $this->rateLimiter->hit($document->user);

        // Log usage statistics
        logger('CV Analysis completed', [
            'document_id' => $document->id,
            'user_id' => $document->user_id,
            'remaining_analyses' => $this->rateLimiter->remaining($document->user),
            'provider' => $result->provider,
            'model' => $result->model,
            'prompt_tokens' => $result->promptTokens,
            'completion_tokens' => $result->completionTokens,
            'cache_write_tokens' => $result->cacheWriteTokens,
            'cache_read_tokens' => $result->cacheReadTokens,
            'cache_hit' => $result->cacheHit,
        ]);

        return $result;
    }

    /**
     * Get the current AI provider name.
     */
    public function getProviderName(): string
    {
        return $this->provider->name();
    }

    /**
     * Get the current AI model being used.
     */
    public function getModel(): string
    {
        return $this->provider->model();
    }

    /**
     * Get remaining analyses for a user.
     */
    public function getRemainingAnalyses(\App\Models\User $user): int
    {
        return $this->rateLimiter->remaining($user);
    }
}

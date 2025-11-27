<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Exceptions\AnalysisRateLimitException;
use App\Models\User;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\DTOs\AnalysisResult;
use App\Services\AI\RateLimiting\AnalysisRateLimiter;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Throwable;

final readonly class RoleAnalysisService
{
    public function __construct(
        private AIProviderInterface $aiProvider,
        private AnalysisRateLimiter $rateLimiter,
    ) {}

    /**
     * @throws AnalysisRateLimitException
     * @throws Throwable
     */
    public function analyze(string $jobDescription, User $user): AnalysisResult
    {
        // Check rate limit
        $this->rateLimiter->check($user);

        // Load system prompt
        $systemPrompt = view('prompts.role-analysis')->render();

        // Build user message for role analysis
        $userMessages = [
            new UserMessage($jobDescription),
        ];

        // Analyze the job description using AI
        $result = $this->aiProvider->analyze($systemPrompt, $userMessages);

        // Record the analysis attempt (decrement available count)
        $this->rateLimiter->hit($user);

        return $result;
    }

    public function getRemainingAnalyses(User $user): int
    {
        return $this->rateLimiter->remaining($user);
    }
}

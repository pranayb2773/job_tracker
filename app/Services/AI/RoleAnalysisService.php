<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Exceptions\AnalysisRateLimitException;
use App\Models\User;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\DTOs\AnalysisResult;
use App\Services\AI\RateLimiting\AnalysisRateLimiter;
use Illuminate\Support\Facades\Log;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use RuntimeException;
use Throwable;

use function json_decode;

final readonly class RoleAnalysisService
{
    public function __construct(
        private AIProviderInterface $aiProvider,
        private AnalysisRateLimiter $rateLimiter,
    )
    {
    }

    /**
     * @param string $jobDescription
     * @param User $user
     *
     * @return AnalysisResult
     *
     * @throws AnalysisRateLimitException
     * @throws Throwable
     */
    public function analyze(string $jobDescription, User $user): AnalysisResult
    {
        // Check rate limit for role analysis
        if (!$this->rateLimiter->attempt($user, 'role_analysis')) {
            $limit = config('ai.role_analysis.rate_limit.daily_limit', 20);
            $resetsAt = $this->rateLimiter->availableAt($user, 'role_analysis');

            throw new AnalysisRateLimitException($limit, 0, $resetsAt->timestamp);
        }


        // Load system prompt
        $systemPrompt = view('prompts.role-analysis')->render();

        // Build user message for role analysis
        $userMessages = [
            new UserMessage($jobDescription)
        ];

        // Analyze the job description using AI
        $result = $this->aiProvider->analyze($systemPrompt, $userMessages);

        // Record the analysis attempt (decrement available count)
        $this->rateLimiter->hit($user, 'role_analysis');

        return $result;
    }

    public function getRemainingAnalyses(User $user): int
    {
        return $this->rateLimiter->remaining($user, 'role_analysis');
    }

    public function hasReachedLimit(User $user): bool
    {
        return $this->rateLimiter->tooManyAttempts($user, 'role_analysis');
    }
}

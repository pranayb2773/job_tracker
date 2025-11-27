<?php

declare(strict_types=1);

namespace App\Services\AI\RateLimiting;

use App\Exceptions\AnalysisRateLimitException;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

final readonly class AnalysisRateLimiter
{
    public function __construct(
        private int $dailyLimit = 30
    ) {}

    /**
     * Check if the user can perform an analysis.
     *
     * @throws AnalysisRateLimitException
     */
    public function check(User $user): void
    {
        $key = $this->getKey($user);

        if (RateLimiter::tooManyAttempts($key, $this->dailyLimit)) {
            throw new AnalysisRateLimitException(
                limit: $this->dailyLimit,
                remaining: 0,
                resetInSeconds: $this->getDecayInSeconds(),
            );
        }
    }

    /**
     * Attempt to perform an analysis (check and hit in one call).
     */
    public function attempt(User $user): bool
    {
        $key = $this->getKey($user);

        if (RateLimiter::tooManyAttempts($key, $this->dailyLimit)) {
            return false;
        }

        $this->hit($user);

        return true;
    }

    /**
     * Check if user has too many attempts.
     */
    public function tooManyAttempts(User $user): bool
    {
        $key = $this->getKey($user);

        return RateLimiter::tooManyAttempts($key, $this->dailyLimit);
    }

    /**
     * Get available at timestamp.
     */
    public function availableAt(User $user): \Carbon\Carbon
    {
        return now()->addSeconds($this->getDecayInSeconds());
    }

    /**
     * Record an analysis attempt.
     */
    public function hit(User $user): void
    {
        $key = $this->getKey($user);
        $decayInSeconds = $this->getDecayInSeconds();

        RateLimiter::hit($key, $decayInSeconds);
    }

    /**
     * Get remaining analyses for the user.
     */
    public function remaining(User $user): int
    {
        $key = $this->getKey($user);

        return max(0, $this->dailyLimit - RateLimiter::attempts($key));
    }

    /**
     * Get the rate limiter key for a user.
     */
    private function getKey(User $user): string
    {
        return "ai_analysis:{$user->id}:daily";
    }

    /**
     * Get decay time in seconds (resets at midnight).
     */
    private function getDecayInSeconds(): int
    {
        // Calculate seconds until midnight
        $now = now();
        $midnight = $now->copy()->addDay()->startOfDay();

        return (int) $now->diffInSeconds($midnight, false);
    }
}

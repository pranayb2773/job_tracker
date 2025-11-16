<?php

declare(strict_types=1);

namespace App\Services\AI\RateLimiting;

use App\Exceptions\AnalysisRateLimitException;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

final readonly class AnalysisRateLimiter
{
    public function __construct(
        private int $dailyLimit = 10,
        private int $roleAnalysisDailyLimit = 20
    ) {}

    /**
     * Check if the user can perform an analysis.
     *
     * @throws AnalysisRateLimitException
     */
    public function check(User $user, string $type = 'cv_analysis'): void
    {
        $key = $this->getKey($user, $type);
        $limit = $this->getLimit($type);

        if (RateLimiter::tooManyAttempts($key, $limit)) {
            throw new AnalysisRateLimitException(
                limit: $limit,
                remaining: 0,
                resetInSeconds: $this->getDecayInSeconds(),
            );
        }
    }

    /**
     * Attempt to perform an analysis (check and hit in one call).
     */
    public function attempt(User $user, string $type = 'cv_analysis'): bool
    {
        $key = $this->getKey($user, $type);
        $limit = $this->getLimit($type);

        if (RateLimiter::tooManyAttempts($key, $limit)) {
            return false;
        }

        $this->hit($user, $type);

        return true;
    }

    /**
     * Check if user has too many attempts.
     */
    public function tooManyAttempts(User $user, string $type = 'cv_analysis'): bool
    {
        $key = $this->getKey($user, $type);
        $limit = $this->getLimit($type);

        return RateLimiter::tooManyAttempts($key, $limit);
    }

    /**
     * Get available at timestamp.
     */
    public function availableAt(User $user, string $type = 'cv_analysis'): \Carbon\Carbon
    {
        return now()->addSeconds($this->getDecayInSeconds());
    }

    /**
     * Record an analysis attempt.
     */
    public function hit(User $user, string $type = 'cv_analysis'): void
    {
        $key = $this->getKey($user, $type);
        $decayInSeconds = $this->getDecayInSeconds();

        RateLimiter::hit($key, $decayInSeconds);
    }

    /**
     * Get remaining analyses for the user.
     */
    public function remaining(User $user, string $type = 'cv_analysis'): int
    {
        $key = $this->getKey($user, $type);
        $limit = $this->getLimit($type);

        return max(0, $limit - RateLimiter::attempts($key));
    }

    /**
     * Get the rate limiter key for a user.
     */
    private function getKey(User $user, string $type = 'cv_analysis'): string
    {
        return "{$type}:{$user->id}:daily";
    }

    /**
     * Get limit based on analysis type.
     */
    private function getLimit(string $type): int
    {
        return match ($type) {
            'role_analysis' => $this->roleAnalysisDailyLimit,
            default => $this->dailyLimit,
        };
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

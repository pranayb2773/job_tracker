<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class AnalysisRateLimitException extends Exception
{
    public function __construct(
        public readonly int $limit,
        public readonly int $remaining,
        public readonly int $resetInSeconds,
    ) {
        $resetTime = now()->addSeconds($resetInSeconds)->format('g:i A');

        parent::__construct(
            "You've reached your daily limit of {$limit} CV analyses. You can analyze more CVs at {$resetTime}."
        );
    }

    public function getRemainingTime(): string
    {
        $hours = floor($this->resetInSeconds / 3600);
        $minutes = floor(($this->resetInSeconds % 3600) / 60);

        if ($hours > 0) {
            return "{$hours} hour".($hours > 1 ? 's' : '')." and {$minutes} minute".($minutes !== 1 ? 's' : '');
        }

        return "{$minutes} minute".($minutes !== 1 ? 's' : '');
    }
}

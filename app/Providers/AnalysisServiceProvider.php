<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\CVAnalysisService;
use App\Services\AI\Providers\ClaudeProvider;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\GroqProvider;
use App\Services\AI\RateLimiting\AnalysisRateLimiter;
use App\Services\AI\RoleAnalysisService;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

final class AnalysisServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind the AI provider based on configuration
        $this->app->bind(AIProviderInterface::class, function ($app) {
            $provider = config('ai.ai_analysis.default_provider');
            $providerConfig = config("ai.providers.{$provider}");

            if (! $providerConfig) {
                throw new InvalidArgumentException("Invalid AI analysis provider: {$provider}");
            }

            return match ($provider) {
                'gemini' => new GeminiProvider(
                    model: $providerConfig['model'],
                    timeout: $providerConfig['timeout'],
                    maxTokens: $providerConfig['max_tokens'],
                ),
                'claude' => new ClaudeProvider(
                    model: $providerConfig['model'],
                    timeout: $providerConfig['timeout'],
                    maxTokens: $providerConfig['max_tokens'],
                ),
                'groq' => new GroqProvider(
                    model: $providerConfig['model'],
                    timeout: $providerConfig['timeout'],
                    maxTokens: $providerConfig['max_tokens'],
                ),
                default => throw new InvalidArgumentException("Unsupported provider: {$provider}"),
            };
        });

        // Bind the Rate Limiter
        $this->app->singleton(AnalysisRateLimiter::class, function ($app) {
            $dailyLimit = config('ai.ai_analysis.rate_limit.daily_limit', 30);

            return new AnalysisRateLimiter(
                dailyLimit: $dailyLimit
            );
        });

        // Bind the CV Analysis Service
        $this->app->singleton(CVAnalysisService::class, function ($app) {
            return new CVAnalysisService(
                provider: $app->make(AIProviderInterface::class),
                rateLimiter: $app->make(AnalysisRateLimiter::class)
            );
        });

        // Bind the Role Analysis Service
        $this->app->singleton(RoleAnalysisService::class, function ($app) {
            return new RoleAnalysisService(
                aiProvider: $app->make(AIProviderInterface::class),
                rateLimiter: $app->make(AnalysisRateLimiter::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

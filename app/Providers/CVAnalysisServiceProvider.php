<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\CVAnalysis\Contracts\AIProviderInterface;
use App\Services\CVAnalysis\CVAnalysisService;
use App\Services\CVAnalysis\Providers\ClaudeProvider;
use App\Services\CVAnalysis\Providers\GeminiProvider;
use App\Services\CVAnalysis\RateLimiting\AnalysisRateLimiter;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

final class CVAnalysisServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind the AI provider based on configuration
        $this->app->bind(AIProviderInterface::class, function ($app) {
            $provider = config('ai.cv_analysis.default_provider');
            $providerConfig = config("ai.cv_analysis.providers.{$provider}");

            if (! $providerConfig) {
                throw new InvalidArgumentException("Invalid CV analysis provider: {$provider}");
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
                default => throw new InvalidArgumentException("Unsupported provider: {$provider}"),
            };
        });

        // Bind the Rate Limiter
        $this->app->singleton(AnalysisRateLimiter::class, function ($app) {
            $dailyLimit = config('ai.cv_analysis.rate_limit.daily_limit', 10);

            return new AnalysisRateLimiter(dailyLimit: $dailyLimit);
        });

        // Bind the CV Analysis Service
        $this->app->singleton(CVAnalysisService::class, function ($app) {
            return new CVAnalysisService(
                provider: $app->make(AIProviderInterface::class),
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

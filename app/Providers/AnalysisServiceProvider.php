<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\Providers\ClaudeProvider;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\RateLimiting\AnalysisRateLimiter;
use App\Services\AI\CVAnalysisService;
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
            $provider = config('ai.cv_analysis.default_provider');
            $providerConfig = config("ai.providers.{$provider}");

            if (!$providerConfig) {
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
            $cvDailyLimit = config('ai.cv_analysis.rate_limit.daily_limit', 10);
            $roleDailyLimit = config('ai.role_analysis.rate_limit.daily_limit', 20);

            return new AnalysisRateLimiter(
                dailyLimit: $cvDailyLimit,
                roleAnalysisDailyLimit: $roleDailyLimit
            );
        });

        // Bind the CV Analysis Service
        $this->app->singleton(CVAnalysisService::class, function ($app) {
            return new CVAnalysisService(
                provider: $app->make(AIProviderInterface::class),
                rateLimiter: $app->make(AnalysisRateLimiter::class)
            );
        });

        // Bind the Role Analysis Service with dedicated AI provider
        $this->app->singleton(RoleAnalysisService::class, function ($app) {
            // Get role analysis provider configuration
            $provider = config('ai.role_analysis.default_provider', config('ai.cv_analysis.default_provider'));
            $baseProviderConfig = config("ai.providers.{$provider}");

            if (!$baseProviderConfig) {
                throw new InvalidArgumentException("Invalid role analysis provider: {$provider}");
            }

            // Create provider with role analysis specific configuration
            $roleAnalysisProvider = match ($provider) {
                'gemini' => new GeminiProvider(
                    model: $baseProviderConfig['model'],
                    timeout: config('ai.role_analysis.timeout', $baseProviderConfig['timeout']),
                    maxTokens: config('ai.role_analysis.max_tokens', 8000),
                ),
                'claude' => new ClaudeProvider(
                    model: $baseProviderConfig['model'],
                    timeout: config('ai.role_analysis.timeout', $baseProviderConfig['timeout']),
                    maxTokens: config('ai.role_analysis.max_tokens', 8000),
                ),
                default => throw new InvalidArgumentException("Unsupported provider: {$provider}"),
            };

            return new RoleAnalysisService(
                aiProvider: $roleAnalysisProvider,
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

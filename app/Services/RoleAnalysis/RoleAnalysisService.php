<?php

declare(strict_types=1);

namespace App\Services\RoleAnalysis;

use App\Exceptions\AnalysisRateLimitException;
use App\Models\User;
use App\Services\CVAnalysis\Contracts\AIProviderInterface;
use App\Services\CVAnalysis\RateLimiting\AnalysisRateLimiter;
use App\Services\RoleAnalysis\DTOs\RoleAnalysisResult;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

use function json_decode;

final readonly class RoleAnalysisService
{
    public function __construct(
        private AIProviderInterface $aiProvider,
        private AnalysisRateLimiter $rateLimiter,
    ) {}

    public function analyze(string $jobDescription, User $user): RoleAnalysisResult
    {
        // Check rate limit for role analysis
        if (! $this->rateLimiter->attempt($user, 'role_analysis')) {
            $limit = config('ai.role_analysis_daily_limit', 20);
            $resetsAt = $this->rateLimiter->availableAt($user, 'role_analysis');

            throw new AnalysisRateLimitException(
                "You have reached your daily limit of {$limit} role analyses. Please try again after {$resetsAt->diffForHumans()}."
            );
        }

        try {
            // Load system prompt
            $systemPrompt = File::get(resource_path('prompts/role-analysis.md'));

            Log::info('Role analysis starting', [
                'prompt_length' => mb_strlen($systemPrompt),
                'job_description_length' => mb_strlen($jobDescription),
                'provider' => $this->aiProvider->name(),
                'model' => $this->aiProvider->model(),
            ]);

            // Analyze the job description using AI
            $response = $this->aiProvider->analyzeText(
                $jobDescription,
                $systemPrompt
            );

            Log::info('AI response received', [
                'response_length' => mb_strlen($response),
                'response_preview' => mb_substr($response, 0, 500),
                'first_char' => mb_substr($response, 0, 1),
                'last_char' => mb_substr($response, -1),
            ]);

            // Parse JSON response
            $analysisData = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to parse role analysis JSON response', [
                    'error' => json_last_error_msg(),
                    'response' => $response,
                ]);

                throw new RuntimeException('Failed to parse AI response. Please try again.');
            }

            Log::info('Role analysis completed successfully');

            return new RoleAnalysisResult(
                data: $analysisData,
                provider: $this->aiProvider->name(),
                model: $this->aiProvider->model(),
            );
        } catch (AnalysisRateLimitException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Role analysis failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new RuntimeException('Role analysis failed. Please try again later.');
        }
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

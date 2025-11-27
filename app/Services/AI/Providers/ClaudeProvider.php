<?php

declare(strict_types=1);

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\DTOs\AnalysisResult;
use App\Services\AI\Providers\Concerns\ParsesAIResponse;
use Exception;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Exceptions\PrismException;
use Prism\Prism\Exceptions\PrismProviderOverloadedException;
use Prism\Prism\Exceptions\PrismRateLimitedException;
use Prism\Prism\Exceptions\PrismRequestTooLargeException;
use Prism\Prism\Prism;

final readonly class ClaudeProvider implements AIProviderInterface
{
    use ParsesAIResponse;

    public function __construct(
        private string $model = 'claude-sonnet-4-5-20250929',
        private int $timeout = 300,
        private int $maxTokens = 8000,
    ) {}

    public function name(): string
    {
        return 'claude';
    }

    public function model(): string
    {
        return $this->model;
    }

    /**
     * @throws Exception
     */
    public function analyze(
        string $systemPrompt,
        array $userMessages
    ): AnalysisResult {
        try {
            $response = Prism::text()
                ->using(Provider::Anthropic, $this->model)
                ->withSystemPrompt($systemPrompt)
                ->withClientOptions([
                    'timeout' => $this->timeout,
                    'connect_timeout' => 60,
                ])
                ->withMessages($userMessages)
                ->withMaxTokens($this->maxTokens)
                ->asText();
        } catch (PrismRateLimitedException $e) {
            logger()->error('AI rate limit exceeded', [
                'systemPrompt' => $systemPrompt,
                'userMessages' => $userMessages,
                'message' => $e->getMessage(),
            ]);

            throw new Exception('AI service rate limit exceeded. Please try again in a few moments.');
        } catch (PrismProviderOverloadedException $e) {
            logger()->error('AI service is currently overloaded', [
                'systemPrompt' => $systemPrompt,
                'userMessages' => $userMessages,
                'message' => $e->getMessage(),
            ]);

            throw new Exception('AI service is currently overloaded. Please try again later.');
        } catch (PrismRequestTooLargeException $e) {
            logger()->error('The text is too large to analyze', [
                'systemPrompt' => $systemPrompt,
                'userMessages' => $userMessages,
                'message' => $e->getMessage(),
            ]);

            throw new Exception('The text is too large to analyze. Please try a shorter description.');
        } catch (PrismException $e) {
            logger()->error('An error occurred while communicating with the AI service', [
                'systemPrompt' => $systemPrompt,
                'userMessages' => $userMessages,
                'message' => $e->getMessage(),
            ]);

            throw new Exception('An error occurred while communicating with the AI service. Please try again later.');
        }

        $analysisData = $this->parseJsonResponse($response->text);

        return new AnalysisResult(
            data: $analysisData,
            promptTokens: $response->usage->promptTokens,
            completionTokens: $response->usage->completionTokens,
            provider: $this->name(),
            model: $this->model,
        );
    }
}

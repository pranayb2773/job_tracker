<?php

declare(strict_types=1);

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\DTOs\AnalysisResult;
use Exception;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Exceptions\PrismException;
use Prism\Prism\Exceptions\PrismProviderOverloadedException;
use Prism\Prism\Exceptions\PrismRateLimitedException;
use Prism\Prism\Exceptions\PrismRequestTooLargeException;
use Prism\Prism\Prism;

final readonly class GeminiProvider implements AIProviderInterface
{
    public function __construct(
        private string $model = 'gemini-2.5-flash',
        private int $timeout = 180,
        private int $maxTokens = 8000,
    ) {}

    public function name(): string
    {
        return 'gemini';
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
                ->using(Provider::Gemini, $this->model)
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

        // Clean the response - remove Markdown code fences if present
        $responseText = $response->text;
        $responseText = preg_replace('/^```json\s*/m', '', $responseText);
        $responseText = preg_replace('/\s*```$/m', '', $responseText);
        $responseText = mb_trim($responseText);

        // Check if response appears truncated (doesn't end with proper JSON closure)
        $isTruncated = ! preg_match('/\}[\s]*$/', $responseText);

        if ($isTruncated) {
            logger('AI text response appears truncated', [
                'response_length' => mb_strlen($responseText),
                'response_end' => mb_substr($responseText, -100),
                'max_tokens' => $this->maxTokens,
                'model' => $this->model,
            ]);

            // Try to recover by completing the JSON if possible
            $recoveredText = $this->attemptJsonRecovery($responseText);
            if ($recoveredText) {
                $responseText = $recoveredText;
                logger('Successfully recovered truncated JSON response');
            } else {
                throw new Exception('AI response was truncated. The analysis may be incomplete. Please try again.');
            }
        }

        // Remove problematic control characters but don't re-encode
        // Remove null bytes and control characters except \t, \n, \r
        $responseText = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $responseText);

        // Parse the JSON response
        $analysisData = json_decode($responseText, true);

        // Validate JSON parsing
        if ($analysisData === null && json_last_error() !== JSON_ERROR_NONE) {
            logger()->error('Failed to parse AI response JSON', [
                'provider' => $this->name(),
                'model' => $this->model,
                'json_error' => json_last_error_msg(),
                'response_snippet' => mb_substr($responseText, 0, 500),
                'response_length' => mb_strlen($responseText),
            ]);

            throw new Exception('Failed to parse AI response: ' . json_last_error_msg() . '. The response may be malformed or incomplete.');
        }

        if (!is_array($analysisData)) {
            logger()->error('AI response is not a valid array', [
                'provider' => $this->name(),
                'model' => $this->model,
                'response_type' => gettype($analysisData),
                'response_snippet' => mb_substr($responseText, 0, 500),
            ]);

            throw new Exception('AI response is not in the expected format. Please try again.');
        }

        return new AnalysisResult(
            data: $analysisData,
            promptTokens: $response->usage->promptTokens,
            completionTokens: $response->usage->completionTokens,
            provider: $this->name(),
            model: $this->model,
        );
    }

    /**
     * Attempt to recover a truncated JSON response.
     */
    private function attemptJsonRecovery(string $truncatedJson): ?string
    {
        // Count opening and closing braces
        $openBraces = mb_substr_count($truncatedJson, '{');
        $closeBraces = mb_substr_count($truncatedJson, '}');

        // If we have more opening braces, try to close them
        if ($openBraces > $closeBraces) {
            $missingBraces = $openBraces - $closeBraces;
            $recovered = $truncatedJson.str_repeat('}', $missingBraces);

            // Test if the recovered JSON is valid
            $decoded = json_decode($recovered, true);
            if ($decoded !== null && json_last_error() === JSON_ERROR_NONE) {
                return $recovered;
            }
        }

        return null;
    }
}

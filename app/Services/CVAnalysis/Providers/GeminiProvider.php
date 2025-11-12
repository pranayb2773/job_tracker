<?php

declare(strict_types=1);

namespace App\Services\CVAnalysis\Providers;

use App\Models\Document;
use App\Services\CVAnalysis\Contracts\AIProviderInterface;
use App\Services\CVAnalysis\DTOs\AnalysisResult;
use Exception;
use Illuminate\Support\Facades\Storage;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Exceptions\PrismException;
use Prism\Prism\Exceptions\PrismProviderOverloadedException;
use Prism\Prism\Exceptions\PrismRateLimitedException;
use Prism\Prism\Exceptions\PrismRequestTooLargeException;
use Prism\Prism\Prism;
use Prism\Prism\ValueObjects\Media\Document as PrismDocument;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;

final readonly class GeminiProvider implements AIProviderInterface
{
    public function __construct(
        private string $model = 'gemini-2.5-flash',
        private int $timeout = 180,
        private int $maxTokens = 4000,
    ) {}

    public function analyze(
        Document $document,
        string $systemPrompt,
        ?array $previousAnalysis = null
    ): AnalysisResult {
        $filePath = Storage::disk('local')->path($document->file_path);

        // Create document for analysis (Gemini has automatic implicit caching)
        $prismDocument = PrismDocument::fromLocalPath($filePath);

        // Build message chain
        $messages = [];

        // Add previous analysis as conversation history if this is a regeneration
        if ($previousAnalysis) {
            $messages[] = new UserMessage(
                'Please analyze this CV/Resume document.',
                [$prismDocument]
            );
            $messages[] = new AssistantMessage(json_encode($previousAnalysis));
            $messages[] = new UserMessage(
                'Please regenerate the analysis with fresh insights. Review the document again carefully and provide an updated, thorough analysis.'
            );
        } else {
            // First-time analysis
            $messages[] = new UserMessage(
                'Please analyze this CV/Resume document.',
                [$prismDocument]
            );
        }

        try {
            $response = Prism::text()
                ->using(Provider::Gemini, $this->model)
                ->withSystemPrompt($systemPrompt)
                ->withClientOptions([
                    'timeout' => $this->timeout,
                    'connect_timeout' => 60,
                ])
                ->withMessages($messages)
                ->withMaxTokens($this->maxTokens)
                ->asText();
        } catch (PrismRateLimitedException $e) {
            throw new Exception('AI service rate limit exceeded. Please try again in a few moments.');
        } catch (PrismProviderOverloadedException $e) {
            throw new Exception('AI service is currently overloaded. Please try again later.');
        } catch (PrismRequestTooLargeException $e) {
            throw new Exception('The document is too large to analyze. Please try a smaller file.');
        } catch (PrismException $e) {
            throw new Exception('An error occurred while communicating with the AI service: '.$e->getMessage());
        }

        // Clean the response - remove Markdown code fences if present
        $responseText = $response->text;
        $responseText = preg_replace('/^```json\s*/m', '', $responseText);
        $responseText = preg_replace('/\s*```$/m', '', $responseText);
        $responseText = mb_trim($responseText);

        // Check if response appears truncated (doesn't end with })
        if (! preg_match('/\}[\s]*$/', $responseText)) {
            logger('AI response appears truncated', [
                'response_length' => mb_strlen($responseText),
                'response_end' => mb_substr($responseText, -100),
            ]);
            throw new Exception('AI response was truncated. The analysis may be incomplete. Please try again.');
        }

        // Sanitize control characters that can cause JSON parsing errors
        $responseText = $this->sanitizeJsonString($responseText);

        // Parse the JSON response
        $analysisData = json_decode($responseText, true);

        if (json_last_error() !== JSON_ERROR_NONE || $analysisData === null) {
            logger('Failed to parse AI response', [
                'error' => json_last_error_msg(),
                'json_error_code' => json_last_error(),
                'response_length' => mb_strlen($responseText),
                'response_start' => mb_substr($responseText, 0, 200),
                'response_end' => mb_substr($responseText, -200),
            ]);
            throw new Exception('Failed to parse AI response: '.json_last_error_msg());
        }

        return new AnalysisResult(
            data: $analysisData,
            promptTokens: $response->usage->promptTokens,
            completionTokens: $response->usage->completionTokens,
            cacheWriteTokens: $response->usage->cacheWriteInputTokens ?? 0,
            cacheReadTokens: $response->usage->cacheReadInputTokens ?? 0,
            cacheHit: ($response->usage->cacheReadInputTokens ?? 0) > 0,
            provider: $this->name(),
            model: $this->model,
        );
    }

    public function name(): string
    {
        return 'gemini';
    }

    public function model(): string
    {
        return $this->model;
    }

    public function analyzeText(
        string $text,
        string $systemPrompt
    ): string {
        $messages = [
            new UserMessage($text),
        ];

        try {
            $response = Prism::text()
                ->using(Provider::Gemini, $this->model)
                ->withSystemPrompt($systemPrompt)
                ->withClientOptions([
                    'timeout' => $this->timeout,
                    'connect_timeout' => 60,
                ])
                ->withMessages($messages)
                ->withMaxTokens($this->maxTokens)
                ->asText();
        } catch (PrismRateLimitedException $e) {
            throw new Exception('AI service rate limit exceeded. Please try again in a few moments.');
        } catch (PrismProviderOverloadedException $e) {
            throw new Exception('AI service is currently overloaded. Please try again later.');
        } catch (PrismRequestTooLargeException $e) {
            throw new Exception('The text is too large to analyze. Please try a shorter description.');
        } catch (PrismException $e) {
            throw new Exception('An error occurred while communicating with the AI service: '.$e->getMessage());
        }

        // Clean the response - remove Markdown code fences if present
        $responseText = $response->text;
        $responseText = preg_replace('/^```json\s*/m', '', $responseText);
        $responseText = preg_replace('/\s*```$/m', '', $responseText);
        $responseText = mb_trim($responseText);

        // Check if response appears truncated (doesn't end with })
        if (! preg_match('/\}[\s]*$/', $responseText)) {
            logger('AI text response appears truncated', [
                'response_length' => mb_strlen($responseText),
                'response_end' => mb_substr($responseText, -100),
            ]);
            throw new Exception('AI response was truncated. The analysis may be incomplete. Please try again.');
        }

        // Remove problematic control characters but don't re-encode
        // Remove null bytes and control characters except \t, \n, \r
        $responseText = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $responseText);

        return $responseText;
    }

    public function analyzeProfileMatching(
        Document $cvDocument,
        string $jobDescription,
        string $jobTitle,
        string $organisation,
        string $systemPrompt
    ): AnalysisResult {
        $filePath = Storage::disk('local')->path($cvDocument->file_path);

        // Create document for analysis (Gemini has automatic implicit caching)
        $prismDocument = PrismDocument::fromLocalPath($filePath);

        // Build the user prompt with job description context
        $userPrompt = "JOB DESCRIPTION:\n{$jobDescription}\n\n";
        $userPrompt .= "ROLE: {$jobTitle}\n";
        $userPrompt .= "ORGANIZATION: {$organisation}\n\n";
        $userPrompt .= 'Please analyze the CV document attached and compare it against the job description above. Provide a comprehensive profile matching analysis.';

        $messages = [
            new UserMessage($userPrompt, [$prismDocument]),
        ];

        try {
            $response = Prism::text()
                ->using(Provider::Gemini, $this->model)
                ->withSystemPrompt($systemPrompt)
                ->withClientOptions([
                    'timeout' => $this->timeout,
                    'connect_timeout' => 60,
                ])
                ->withProviderOptions([
                    'responseMimeType' => 'application/json',
                ])
                ->withMessages($messages)
                ->withMaxTokens(8000) // Increased for comprehensive profile matching
                ->asText();
        } catch (PrismRateLimitedException $e) {
            throw new Exception('AI service rate limit exceeded. Please try again in a few moments.');
        } catch (PrismProviderOverloadedException $e) {
            throw new Exception('AI service is currently overloaded. Please try again later.');
        } catch (PrismRequestTooLargeException $e) {
            throw new Exception('The document or job description is too large to analyze. Please try shorter content.');
        } catch (PrismException $e) {
            throw new Exception('An error occurred while communicating with the AI service: '.$e->getMessage());
        }

        // Clean the response
        $responseText = $response->text;
        $responseText = preg_replace('/^```json\s*/m', '', $responseText);
        $responseText = preg_replace('/\s*```$/m', '', $responseText);
        $responseText = mb_trim($responseText);

        // Check if response appears truncated; if so, attempt a compact fallback
        if (! preg_match('/\}[\s]*$/', $responseText)) {
            logger('Profile matching response appears truncated', [
                'response_length' => mb_strlen($responseText),
                'response_end' => mb_substr($responseText, -100),
            ]);

            // Fallback attempt: request a more compact JSON, smaller token budget, no responseMimeType constraint
            try {
                $compactSystemPrompt = $systemPrompt.'\n\nReturn strictly valid JSON only. Keep text concise and arrays to max 10 items.';

                $fallback = Prism::text()
                    ->using(Provider::Gemini, $this->model)
                    ->withSystemPrompt($compactSystemPrompt)
                    ->withClientOptions([
                        'timeout' => $this->timeout,
                        'connect_timeout' => 60,
                    ])
                    ->withMessages($messages)
                    ->withMaxTokens(4000)
                    ->asText();

                $responseText = mb_trim($fallback->text ?? '');
                $responseText = preg_replace('/^```json\s*/m', '', $responseText);
                $responseText = preg_replace('/\s*```$/m', '', $responseText);

                if (! preg_match('/\}[\s]*$/', $responseText)) {
                    throw new Exception('AI response was truncated after fallback.');
                }
            } catch (Throwable $fallbackError) {
                throw new Exception('AI response was truncated. The analysis may be incomplete. Please try again.');
            }
        }

        // Sanitize control characters
        $responseText = $this->sanitizeJsonString($responseText);

        // Parse the JSON response
        $analysisData = json_decode($responseText, true);

        if (json_last_error() !== JSON_ERROR_NONE || $analysisData === null) {
            logger('Failed to parse profile matching response', [
                'error' => json_last_error_msg(),
                'json_error_code' => json_last_error(),
                'response_length' => mb_strlen($responseText),
                'response_start' => mb_substr($responseText, 0, 200),
                'response_end' => mb_substr($responseText, -200),
            ]);
            throw new Exception('Failed to parse AI response: '.json_last_error_msg());
        }

        return new AnalysisResult(
            data: $analysisData,
            promptTokens: $response->usage->promptTokens,
            completionTokens: $response->usage->completionTokens,
            cacheWriteTokens: $response->usage->cacheWriteInputTokens ?? 0,
            cacheReadTokens: $response->usage->cacheReadInputTokens ?? 0,
            cacheHit: ($response->usage->cacheReadInputTokens ?? 0) > 0,
            provider: $this->name(),
            model: $this->model,
        );
    }

    /**
     * Sanitize JSON string by removing or escaping problematic control characters.
     */
    private function sanitizeJsonString(string $json): string
    {
        // First, ensure UTF-8 encoding
        if (! mb_check_encoding($json, 'UTF-8')) {
            $json = mb_convert_encoding($json, 'UTF-8', 'UTF-8');
        }

        // Try to decode with JSON_INVALID_UTF8_IGNORE flag first
        $decoded = json_decode($json, true, 512, JSON_INVALID_UTF8_IGNORE);

        if ($decoded !== null) {
            // If successful, re-encode cleanly
            return json_encode($decoded);
        }

        // If that didn't work, remove problematic control characters
        // Remove null bytes and control characters except \t, \n, \r
        $json = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $json);

        return $json;
    }
}

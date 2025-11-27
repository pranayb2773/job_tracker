<?php

declare(strict_types=1);

namespace App\Services\AI\Providers\Concerns;

use Exception;

trait ParsesAIResponse
{
    /**
     * Parse and validate AI response text as JSON.
     *
     * @throws Exception
     */
    protected function parseJsonResponse(string $responseText): array
    {
        $cleanedText = $this->cleanResponse($responseText);
        $validatedText = $this->handleTruncation($cleanedText);
        $sanitizedText = $this->sanitizeControlCharacters($validatedText);

        return $this->decodeAndValidateJson($sanitizedText);
    }

    /**
     * Clean the response by removing Markdown code fences.
     */
    private function cleanResponse(string $responseText): string
    {
        $cleaned = preg_replace('/^```json\s*/m', '', $responseText);
        $cleaned = preg_replace('/\s*```$/m', '', $cleaned);

        return mb_trim($cleaned);
    }

    /**
     * Check if response is truncated and attempt recovery.
     *
     * @throws Exception
     */
    private function handleTruncation(string $responseText): string
    {
        $isTruncated = ! preg_match('/\}[\s]*$/', $responseText);

        if (! $isTruncated) {
            return $responseText;
        }

        $this->logTruncation($responseText);

        $recoveredText = $this->attemptJsonRecovery($responseText);

        if ($recoveredText) {
            logger('Successfully recovered truncated JSON response');

            return $recoveredText;
        }

        throw new Exception('AI response was truncated. The analysis may be incomplete. Please try again.');
    }

    /**
     * Log truncation details.
     */
    private function logTruncation(string $responseText): void
    {
        logger('AI text response appears truncated', [
            'response_length' => mb_strlen($responseText),
            'response_end' => mb_substr($responseText, -100),
            'max_tokens' => $this->maxTokens,
            'model' => $this->model,
        ]);
    }

    /**
     * Attempt to recover a truncated JSON response.
     */
    private function attemptJsonRecovery(string $truncatedJson): ?string
    {
        $openBraces = mb_substr_count($truncatedJson, '{');
        $closeBraces = mb_substr_count($truncatedJson, '}');

        if ($openBraces <= $closeBraces) {
            return null;
        }

        $missingBraces = $openBraces - $closeBraces;
        $recovered = $truncatedJson.str_repeat('}', $missingBraces);

        $decoded = json_decode($recovered, true);

        if ($decoded !== null && json_last_error() === JSON_ERROR_NONE) {
            return $recovered;
        }

        return null;
    }

    /**
     * Remove problematic control characters.
     */
    private function sanitizeControlCharacters(string $text): string
    {
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $text);
    }

    /**
     * Decode JSON and validate the result.
     *
     * @throws Exception
     */
    private function decodeAndValidateJson(string $jsonText): array
    {
        $decoded = json_decode($jsonText, true);

        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            $this->logJsonParsingError($jsonText);

            throw new Exception(
                'Failed to parse AI response: '.json_last_error_msg().
                '. The response may be malformed or incomplete.'
            );
        }

        if (! is_array($decoded)) {
            $this->logInvalidArrayError($decoded, $jsonText);

            throw new Exception('AI response is not in the expected format. Please try again.');
        }

        return $decoded;
    }

    /**
     * Log JSON parsing errors.
     */
    private function logJsonParsingError(string $responseText): void
    {
        logger()->error('Failed to parse AI response JSON', [
            'provider' => $this->name(),
            'model' => $this->model,
            'json_error' => json_last_error_msg(),
            'response_snippet' => mb_substr($responseText, 0, 500),
            'response_length' => mb_strlen($responseText),
        ]);
    }

    /**
     * Log invalid array type errors.
     */
    private function logInvalidArrayError(mixed $decoded, string $responseText): void
    {
        logger()->error('AI response is not a valid array', [
            'provider' => $this->name(),
            'model' => $this->model,
            'response_type' => gettype($decoded),
            'response_snippet' => mb_substr($responseText, 0, 500),
        ]);
    }
}

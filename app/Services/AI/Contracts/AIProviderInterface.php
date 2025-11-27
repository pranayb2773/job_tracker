<?php

declare(strict_types=1);

namespace App\Services\AI\Contracts;

use App\Services\AI\DTOs\AnalysisResult;

interface AIProviderInterface
{
    /**
     * Analyze text content using AI.
     */
    public function analyze(
        string $systemPrompt,
        array $userMessages
    ): AnalysisResult;

    /**
     * Get the provider name.
     */
    public function name(): string;

    /**
     * Get the model being used.
     */
    public function model(): string;
}

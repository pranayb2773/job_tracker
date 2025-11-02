<?php

declare(strict_types=1);

namespace App\Services\CVAnalysis\Contracts;

use App\Models\Document;
use App\Services\CVAnalysis\DTOs\AnalysisResult;

interface AIProviderInterface
{
    /**
     * Analyze a CV/Resume document using AI.
     */
    public function analyze(
        Document $document,
        string $systemPrompt,
        ?array $previousAnalysis = null
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

<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Exceptions\AnalysisRateLimitException;
use App\Models\Document;
use App\Models\User;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\DTOs\AnalysisResult;
use App\Services\AI\RateLimiting\AnalysisRateLimiter;
use Exception;
use Illuminate\Support\Facades\Storage;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Smalot\PdfParser\Parser;
use Throwable;

final readonly class CVAnalysisService
{
    public function __construct(
        private AIProviderInterface $provider,
        private AnalysisRateLimiter $rateLimiter
    ) {}

    /**
     * Analyze a CV/Resume document using the configured AI provider.
     *
     * @throws AnalysisRateLimitException
     * @throws Exception
     * @throws Throwable
     */
    public function analyze(Document $document): AnalysisResult
    {
        // Check rate limit before proceeding
        $this->rateLimiter->check($document->user);

        // Increase PHP execution time for PDF processing
        set_time_limit(300); // 5 minutes

        // Load system prompt
        $systemPrompt = view('prompts.cv-analysis')->render();
        $userMessages = $this->buildUserPrompt($document);

        // Delegate to the AI provider
        $result = $this->provider->analyze($systemPrompt, $userMessages);

        // Record the analysis attempt (decrement available count)
        $this->rateLimiter->hit($document->user);

        return $result;
    }

    /**
     * Get the current AI provider name.
     */
    public function getProviderName(): string
    {
        return $this->provider->name();
    }

    /**
     * Get the current AI model being used.
     */
    public function getModel(): string
    {
        return $this->provider->model();
    }

    /**
     * Get remaining analyses for a user.
     */
    public function getRemainingAnalyses(User $user): int
    {
        return $this->rateLimiter->remaining($user);
    }

    /**
     * @throws Exception
     */
    private function getDocumentText(Document $document): string
    {
        $filePath = Storage::disk('local')->path($document->file_path);

        if (! file_exists($filePath)) {
            throw new Exception('File does not exist.');
        }

        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);

        return $pdf->getText();
    }

    private function buildUserPrompt(Document $document): array
    {
        // Get previous analysis if this is a regeneration
        $previousAnalysis = $document->analysis && $document->analyzed_at
            ? $document->analysis
            : null;

        // Build message chain
        $messages = [];

        $messages[] = new UserMessage(
            'Please analyze this CV/Resume document.'.PHP_EOL.PHP_EOL.$this->getDocumentText($document),
        );

        // Add previous analysis as conversation history if this is a regeneration
        if ($previousAnalysis) {
            $messages[] = new AssistantMessage(json_encode($previousAnalysis));
            $messages[] = new UserMessage(
                'Please regenerate the analysis with fresh insights. Review the CV/Resume content again carefully and provide an updated, thorough analysis.'
            );
        }

        return $messages;
    }
}

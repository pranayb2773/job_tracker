<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Enums\DocumentType;
use App\Exceptions\AnalysisRateLimitException;
use App\Models\Document;
use App\Models\JobApplication;
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

final readonly class ApplicationAIService
{
    public function __construct(
        private AIProviderInterface $provider,
        private AnalysisRateLimiter $rateLimiter
    ) {}

    /**
     * Generate a cover letter for a job application using AI based on the job description and CV/Resume document.
     *
     * @throws AnalysisRateLimitException
     * @throws Exception
     * @throws Throwable
     */
    public function generateCoverLetter(JobApplication $application): AnalysisResult
    {
        // Check rate limit before proceeding
        $this->rateLimiter->check($application->user);

        // Increase PHP execution time for PDF processing
        set_time_limit(300); // 5 minutes

        // Load system prompt
        $systemPrompt = view('prompts.cover-letter')
            ->with([
                'role' => $application->job_title,
                'organisation' => $application->organisation,
            ])
            ->render();

        $userMessages = $this->buildUserPromptForCoverLetter($application);

        // Delegate to the AI provider
        $result = $this->provider->analyze($systemPrompt, $userMessages);

        // Record the analysis attempt (decrement available count)
        $this->rateLimiter->hit($application->user);

        return $result;
    }

    /**
     * @throws AnalysisRateLimitException
     * @throws Throwable
     */
    public function generateProfileMatchingAnalysis(JobApplication $application): AnalysisResult
    {
        // Check rate limit before proceeding
        $this->rateLimiter->check($application->user);

        // Increase PHP execution time for PDF processing
        set_time_limit(300); // 5 minutes

        // Load system prompt
        $systemPrompt = view('prompts.profile-matching')->render();

        $userMessages = $this->buildUserPromptForProfileMatching($application);

        // Delegate to the AI provider
        $result = $this->provider->analyze($systemPrompt, $userMessages);

        // Record the analysis attempt (decrement available count)
        $this->rateLimiter->hit($application->user);

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

    private function buildUserPromptForCoverLetter(JobApplication $application): array
    {
        // Get previous analysis if this is a regeneration
        $previousAnalysis = $application?->cover_letter;

        // Get CV document
        $document = $application->documents()->firstWhere('type', DocumentType::CurriculumVitae->value);

        // Get the job description
        $descHtml = (string) ($application->job_description ?? '');
        $desc = mb_trim(strip_tags($descHtml));

        // Build message chain
        $messages = [];

        $messages[] = new UserMessage(
            'Please analyze the CV document attached and compare it against the job description above. Provide a comprehensive profile matching analysis.'.PHP_EOL.PHP_EOL.$this->getDocumentText($document).PHP_EOL.PHP_EOL.$desc,
        );

        // Add previous analysis as conversation history if this is a regeneration
        if ($previousAnalysis) {
            $messages[] = new AssistantMessage(json_encode($previousAnalysis));
            $messages[] = new UserMessage(
                'Please regenerate the cover letter with fresh content. Review the job description and CV/Resume content again carefully and provide an updated cover letter.'
            );
        }

        return $messages;
    }

    private function buildUserPromptForProfileMatching(JobApplication $application): array
    {
        // Get previous analysis if this is a regeneration
        $previousAnalysis = $application?->profile_matching;

        // Get CV document
        $document = $application->documents()->firstWhere('type', DocumentType::CurriculumVitae->value);

        // Get the job description
        $descHtml = (string) ($application->job_description ?? '');
        $desc = mb_trim(strip_tags($descHtml));

        // Build message chain
        $messages = [];

        $messages[] = new UserMessage(
            'Please generate cover letter using job description and CV/Resume document.'.PHP_EOL.PHP_EOL.$this->getDocumentText($document).PHP_EOL.PHP_EOL.$desc,
        );

        // Add previous analysis as conversation history if this is a regeneration
        if ($previousAnalysis) {
            $messages[] = new AssistantMessage(json_encode($previousAnalysis));
            $messages[] = new UserMessage(
                'Please regenerate the profile matching analysis with fresh insights. Review the CV/Resume and job description content again carefully and provide an updated, thorough analysis.'
            );
        }

        return $messages;
    }
}

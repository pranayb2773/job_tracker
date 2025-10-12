<?php

declare(strict_types=1);

namespace App\Livewire\Document;

use App\Models\Document;
use Exception;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Prism\Prism\Prism;
use Smalot\PdfParser\Parser;

final class AnalyzeDocument extends Component
{
    public Document $document;

    public ?array $analysis = null;

    public bool $isAnalyzing = false;

    public function mount(Document $document): void
    {
        // Ensure user owns the document
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        $this->document = $document;

        // Load existing analysis if available
        if ($document->analysis) {
            $this->analysis = $document->analysis;
        }
    }

    public function analyzeCV(): void
    {
        $this->isAnalyzing = true;

        try {
            // Extract text from PDF
            $pdfText = $this->extractPdfText();

            if (empty($pdfText)) {
                Flux::toast(
                    text: 'Could not extract text from PDF.',
                    heading: 'Analysis Failed',
                    variant: 'danger',
                );
                $this->isAnalyzing = false;

                return;
            }

            // Analyze CV using AI
            $this->analysis = $this->analyzeWithAI($pdfText);

            // Store analysis in database
            $this->document->update([
                'analysis' => $this->analysis,
                'analyzed_at' => now(),
            ]);

            Flux::toast(
                text: 'CV analysis completed successfully.',
                heading: 'Analysis Complete',
                variant: 'success',
            );
        } catch (Exception $e) {
            logger($e->getMessage());
            Flux::toast(
                text: 'An error occurred during analysis: '.$e->getMessage(),
                heading: 'Analysis Failed',
                variant: 'danger',
            );
        } finally {
            $this->isAnalyzing = false;
        }
    }

    public function render(): View
    {
        return view('livewire.document.analyze-document')
            ->title(config('app.name').' | Analyze '.$this->document->title);
    }

    protected function extractPdfText(): string
    {
        $filePath = Storage::disk('local')->path($this->document->file_path);

        $parser = new Parser;
        $pdf = $parser->parseFile($filePath);

        return $pdf->getText();
    }

    protected function analyzeWithAI(string $cvText): array
    {
        $prompt = $this->buildAnalysisPrompt($cvText);

        $response = Prism::text()
            ->using('anthropic', 'claude-sonnet-4-20250514')
            ->withPrompt($prompt)
            ->withMaxTokens(4000)
            ->asText();

        // Clean the response - remove markdown code fences if present
        $responseText = $response->text;
        $responseText = preg_replace('/^```json\s*/m', '', $responseText);
        $responseText = preg_replace('/\s*```$/m', '', $responseText);
        $responseText = trim($responseText);

        // Parse the JSON response
        $analysisData = json_decode($responseText, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            logger('JSON decode error: '.json_last_error_msg());
            logger('Response text: '.$responseText);
            throw new Exception('Failed to parse AI response: '.json_last_error_msg());
        }

        return $analysisData;
    }

    protected function buildAnalysisPrompt(string $cvText): string
    {
        $promptTemplate = file_get_contents(resource_path('prompts/cv-analysis.md'));

        return str_replace('{{ CV_TEXT }}', $cvText, $promptTemplate);
    }
}

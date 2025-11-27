<?php

declare(strict_types=1);

namespace App\Livewire\Document;

use App\Exceptions\AnalysisRateLimitException;
use App\Models\Document;
use App\Services\AI\CVAnalysisService;
use Exception;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class AnalyzeDocument extends Component
{
    public Document $document;

    public ?array $analysis = null;

    public bool $isAnalyzing = false;

    public function mount(Document $document): void
    {
        // Ensure the user owns the document
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        $this->document = $document;

        // Load existing analysis if available
        $this->analysis = $document?->analysis;
    }

    public function analyzeCV(CVAnalysisService $cvAnalysisService): void
    {
        $this->isAnalyzing = true;

        try {
            // Analyze CV using the configured AI provider (via service)
            $result = $cvAnalysisService->analyze($this->document);

            // Store analysis in database
            $this->analysis = $result->data;
            $this->document->update([
                'analysis' => $result->data,
                'analyzed_at' => now(),
            ]);

            logger()->debug('AnalyzeDocument stored CV analysis', [
                'document_id' => $this->document->id,
                'has_analysis' => $this->document->analysis !== null,
            ]);

            // Get remaining analyses for user feedback
            $remaining = $cvAnalysisService->getRemainingAnalyses(Auth::user());

            Flux::toast(
                text: "CV analysis completed successfully using {$result->provider}. You have {$remaining} analyses remaining today.",
                heading: 'Analysis Complete',
                variant: 'success',
            );
        } catch (AnalysisRateLimitException $e) {
            logger('Rate limit exceeded', [
                'user_id' => Auth::id(),
                'limit' => $e->limit,
                'remaining' => $e->remaining,
            ]);

            Flux::toast(
                text: $e->getMessage() . ' (' . $e->getRemainingTime() . ' remaining)',
                heading: 'Daily Limit Reached',
                variant: 'warning',
            );
        } catch (Exception $e) {
            logger()->error('AnalyzeDocument CV analysis failed', [
                'document_id' => $this->document->id,
                'error' => $e->getMessage(),
            ]);
            Flux::toast(
                text: $e->getMessage(),
                heading: 'Analysis Failed',
                variant: 'danger',
            );
        } finally {
            $this->isAnalyzing = false;
        }
    }

    public function downloadPDF(): StreamedResponse
    {
        if (!$this->analysis) {
            Flux::toast(
                text: 'No analysis available to download.',
                heading: 'Download Failed',
                variant: 'danger',
            );

            return response()->streamDownload(fn() => '', '');
        }

        $html = view('pdf.cv-analysis', [
            'document' => $this->document,
            'analysis' => $this->analysis,
        ])->render();

        $fileName = str($this->document->title)
            ->slug()
            ->append('-analysis.pdf')
            ->toString();

        // Generate PDF to temporary file
        $tempPath = storage_path('app/temp/' . uniqid('pdf_') . '.pdf');

        Pdf::html($html)
            ->format('A4')
            ->withBrowsershot(function (Browsershot $browsershot) {
                $browsershot->scale(0.6);
                $browsershot->margins(10, 10, 10, 10);
            })
            ->save($tempPath);

        // Stream the file and delete after
        return response()->streamDownload(function () use ($tempPath) {
            echo file_get_contents($tempPath);
            @unlink($tempPath);
        }, $fileName);
    }

    public function render(): View
    {
        return view('livewire.document.analyze-document')
            ->title(config('app.name') . ' | Analyze ' . $this->document->title);
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\Document;

use App\Exceptions\AnalysisRateLimitException;
use App\Jobs\ProcessCVAnalysis;
use App\Models\Document;
use App\Services\CVAnalysis\CVAnalysisService;
use Exception;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\On;
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
        if ($document->analysis) {
            $this->analysis = $document->analysis;
        }
    }

    public function analyzeCV(CVAnalysisService $cvAnalysisService): void
    {
        try {
            // Check rate limit before dispatching job
            if ($cvAnalysisService->hasReachedLimit(Auth::user())) {
                $limit = config('ai.cv_analysis.rate_limit.daily_limit', 10);
                Flux::toast(
                    text: "You have reached your daily limit of {$limit} CV analyses. Please try again tomorrow.",
                    heading: 'Daily Limit Reached',
                    variant: 'warning',
                );
                return;
            }
        } catch (AnalysisRateLimitException $e) {
            Flux::toast(
                text: $e->getMessage() . ' (' . $e->getRemainingTime() . ' remaining)',
                heading: 'Daily Limit Reached',
                variant: 'warning',
            );
            return;
        }

        $this->isAnalyzing = true;
        $this->analysis = null; // Clear old data to prevent false success message

        // Dispatch job to process in background
        ProcessCVAnalysis::dispatch($this->document);

        Flux::toast(
            text: 'CV analysis started. This may take 2-3 minutes. The page will update automatically when complete.',
            heading: 'Processing...',
            variant: 'info',
        );
    }

    #[On('refresh-cv-analysis')]
    public function refreshAnalysis(): void
    {
        // Refresh the document model from database
        $this->document = $this->document->fresh();
        $newData = $this->document->analysis ?? null;

        // Only show success if we actually got NEW data (wasn't null before)
        if ($newData && !$this->analysis && $this->isAnalyzing) {
            $this->isAnalyzing = false;
            $this->analysis = $newData;

            // Get remaining analyses for user feedback
            $cvAnalysisService = app(CVAnalysisService::class);
            $remaining = $cvAnalysisService->getRemainingAnalyses(Auth::user());

            Flux::toast(
                text: "CV analysis completed successfully. You have {$remaining} analyses remaining today.",
                heading: 'Analysis Complete',
                variant: 'success',
            );
        } else {
            $this->analysis = $newData;
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

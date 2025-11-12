<?php

declare(strict_types=1);

namespace App\Livewire\Application;

use App\Enums\DocumentType;
use App\Exceptions\AnalysisRateLimitException;
use App\Models\JobApplication;
use App\Services\CVAnalysis\Contracts\AIProviderInterface;
use App\Services\CVAnalysis\RateLimiting\AnalysisRateLimiter;
use App\Services\RoleAnalysis\RoleAnalysisService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class ViewApplication extends Component
{
    public JobApplication $application;

    public ?array $roleAnalysis = null;

    public bool $isAnalyzingRole = false;

    public int $remainingAnalyses = 0;

    public ?string $coverLetter = null;

    public ?array $profileMatching = null;

    /**
     * Chart data for keyword analysis (Flux chart wire:model source).
     * Each item shape: ['keyword' => string, 'resume' => int, 'jd' => int]
     * Values are absolute frequencies (integers).
     *
     * @var array<int, array{keyword:string,resume:int,jd:int}>
     */
    public array $data = [];

    /**
     * Max keyword frequency used for Y-axis tick end.
     */
    public int $maxKeywordFreq = 0;

    /**
     * "Nice" Y-axis maximum ensuring at least 5 integer ticks.
     */
    public int $yAxisMax = 0;

    public function mount(JobApplication $application): void
    {
        // Ensure the user owns the application
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $this->application = $application->load('documents');
        $this->roleAnalysis = $this->application->role_analysis ?? null;
        $this->coverLetter = is_array($this->application->cover_letter ?? null)
            ? (mb_trim((string)($this->application->cover_letter['content'] ?? '')) ?: null)
            : null;
        $this->profileMatching = $this->application->profile_matching ?? null;
        $this->computeKeywordChartData();

        // Initialize remaining role analyses
        $service = app(RoleAnalysisService::class);
        $this->remainingAnalyses = $service->getRemainingAnalyses(Auth::user());
    }

    public function render(): View
    {
        return view('livewire.application.view-application')
            ->title(config('app.name') . ' | ' . $this->application->job_title);
    }

    public function analyzeRole(RoleAnalysisService $service): void
    {
        $desc = (string)($this->application->job_description ?? '');
        if (mb_strlen(strip_tags($desc)) < 100) {
            Flux::toast(
                text: 'Please add a longer job description (min 100 chars).',
                heading: 'Job Description Required',
                variant: 'warning',
            );

            return;
        }

        $this->isAnalyzingRole = true;
        try {
            set_time_limit(120);
            $result = $service->analyze($desc, Auth::user());
            $this->roleAnalysis = $result->data;

            // Persist to application JSON column
            $this->application->role_analysis = $this->roleAnalysis;
            $this->application->save();

            $this->remainingAnalyses = $service->getRemainingAnalyses(Auth::user());

            Flux::toast(
                text: 'Role analysis completed successfully.',
                heading: 'Analysis Complete',
                variant: 'success',
            );
        } catch (AnalysisRateLimitException $e) {
            Flux::toast(
                text: $e->getMessage(),
                heading: 'Daily Limit Reached',
                variant: 'warning',
            );
        } catch (Throwable $e) {
            Flux::toast(
                text: 'An error occurred while analyzing the role. Please try again.',
                heading: 'Analysis Failed',
                variant: 'danger',
            );
            logger()->error('Role analysis failed', [
                'user_id' => Auth::id(),
                'application_id' => $this->application->id,
                'error' => $e->getMessage(),
            ]);
        } finally {
            $this->isAnalyzingRole = false;
        }
    }

    public function generateCoverLetter(AIProviderInterface $provider): void
    {
        $descHtml = (string)($this->application->job_description ?? '');
        $desc = mb_trim(strip_tags($descHtml));
        if (mb_strlen($desc) < 60) {
            Flux::toast(
                text: 'Please add a more complete job description to generate a cover letter.',
                heading: 'Job Description Required',
                variant: 'warning',
            );

            return;
        }

        // Rate limit (reuse role_analysis bucket)
        $limiter = app(AnalysisRateLimiter::class);
        try {
            $limiter->check(Auth::user(), 'role_analysis');
        } catch (Throwable $e) {
            Flux::toast(
                text: 'Daily limit reached for AI generation. Try again tomorrow.',
                heading: 'Limit Reached',
                variant: 'warning',
            );

            return;
        }

        $systemPrompt = mb_trim((string)view('prompts.cover-letter')->render());
        if ($systemPrompt === '') {
            $systemPrompt = 'Draft a concise, UK English cover letter using the provided job description and CV attachment. Keep under 350 words.';
        }

        $cv = $this->application->documents->first(
            fn($d) => $d->type?->value === DocumentType::CurriculumVitae->value
        );

        $input = "JOB DESCRIPTION:\n" . $desc;
        // Provide CV filename context; attachment handling is provider-specific
        if ($cv) {
            $input .= "\n\nCV FILENAME:\n" . ($cv->file_name ?? 'cv.pdf') . "\n";
        }
        $input .= "\n\nROLE: " . ($this->application->job_title ?? '');
        $input .= "\nORG: " . ($this->application->organisation ?? '');

        try {
            set_time_limit(120);
            $response = $provider->analyzeText($input, $systemPrompt);
            // Provider returns JSON text; decode and extract content robustly
            $payload = null;
            $decoded = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (is_array($decoded)) {
                    $this->coverLetter = isset($decoded['content'])
                        ? mb_trim((string)$decoded['content'])
                        : mb_trim($response);
                    // Store full decoded object and add generated_at
                    $payload = $decoded;
                    $payload['generated_at'] = now()->toIso8601String();
                } elseif (is_string($decoded)) {
                    // JSON response was a quoted string
                    $this->coverLetter = mb_trim($decoded);
                    $payload = [
                        'content' => $this->coverLetter,
                        'generated_at' => now()->toIso8601String(),
                    ];
                }
            }
            if ($payload === null) {
                // Fallback to raw text
                $this->coverLetter = mb_trim($response);
                $payload = [
                    'content' => $this->coverLetter,
                    'generated_at' => now()->toIso8601String(),
                ];
            }
            $this->application->cover_letter = $payload;
            $this->application->save();

            // consume one unit
            $limiter->hit(Auth::user(), 'role_analysis');

            Flux::toast(
                text: 'Cover letter generated successfully.',
                heading: 'Done',
                variant: 'success',
            );
        } catch (Throwable $e) {
            Flux::toast(
                text: 'Failed to generate cover letter. Please try again.',
                heading: 'Generation Failed',
                variant: 'danger',
            );
            logger()->error('Cover letter generation failed', [
                'user_id' => Auth::id(),
                'application_id' => $this->application->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function downloadDocument(int $documentId): ?StreamedResponse
    {
        $document = $this->application
            ->whereBelongsTo(Auth::user())
            ->documents()
            ->where('documents.id', $documentId)
            ->firstOrFail();

        if (!Storage::disk('local')->exists($document->file_path)) {
            Flux::toast(
                text: 'File not found.',
                heading: 'Download Failed',
                variant: 'danger',
            );

            return null;
        }

        return Storage::disk('local')->download(
            $document->file_path,
            $document->file_name
        );
    }

    public function downloadCoverLetter(): ?StreamedResponse
    {
        $content = (string)($this->application->cover_letter['content'] ?? $this->coverLetter ?? '');
        if ($content === '') {
            Flux::toast(
                text: 'No cover letter to download yet.',
                heading: 'Nothing to Download',
                variant: 'warning',
            );

            return null;
        }

        $fileName = 'cover-letter-' . now()->format('Y-m-d-His') . '.txt';

        return response()->streamDownload(function () use ($content): void {
            echo $content;
        }, $fileName, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function generateProfileMatching(AIProviderInterface $provider): void
    {
        $descHtml = (string)($this->application->job_description ?? '');
        $desc = mb_trim(strip_tags($descHtml));
        // Keep description within a safe upper bound to avoid token bloat
        $desc = mb_substr($desc, 0, 6000);
        if (mb_strlen($desc) < 60) {
            Flux::toast(
                text: 'Please add a more complete job description to run profile matching.',
                heading: 'Job Description Required',
                variant: 'warning',
            );

            return;
        }

        // Find the CV document
        $cv = $this->application->documents->first(
            fn($d) => $d->type?->value === DocumentType::CurriculumVitae->value
        );

        // Require CV for profile matching
        if (!$cv) {
            Flux::toast(
                text: 'A comprehensive profile matching analysis cannot be performed without a CV. Please upload your CV first.',
                heading: 'CV Required',
                variant: 'warning',
            );

            return;
        }

        $limiter = app(AnalysisRateLimiter::class);
        try {
            $limiter->check(Auth::user(), 'role_analysis');
        } catch (Throwable $e) {
            Flux::toast(
                text: 'Daily limit reached for AI generation. Try again tomorrow.',
                heading: 'Limit Reached',
                variant: 'warning',
            );

            return;
        }

        $systemPrompt = mb_trim((string)view('prompts.profile-matching')->render());
        if ($systemPrompt === '') {
            $systemPrompt = 'Analyze the job description and CV to provide a comprehensive profile matching analysis with scores, strengths, gaps, keyword analysis, skills analysis, experience match, education match, and actionable suggestions.';
        }

        try {
            set_time_limit(180);

            // Use the dedicated profile matching method
            $result = $provider->analyzeProfileMatching(
                cvDocument: $cv,
                jobDescription: $desc,
                jobTitle: $this->application->job_title ?? 'Not specified',
                organisation: $this->application->organisation ?? 'Not specified',
                systemPrompt: $systemPrompt
            );

            $decoded = $result->data;
            if (!is_array($decoded)) {
                throw new RuntimeException('Profile matching: invalid response format');
            }

            $this->profileMatching = $decoded;
            $this->application->profile_matching = $decoded;
            $this->application->save();

            // Refresh keyword chart data from the new profile matching payload
            $this->computeKeywordChartData();

            $limiter->hit(Auth::user(), 'role_analysis');

            Flux::toast(
                text: 'Profile matching generated successfully.',
                heading: 'Done',
                variant: 'success',
            );
        } catch (Throwable $e) {
            Flux::toast(
                text: $e->getMessage(),
                heading: 'Generation Failed',
                variant: 'danger',
            );
            logger()->error('Profile matching generation failed', [
                'user_id' => Auth::id(),
                'application_id' => $this->application->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Build the normalized chart dataset from profile matching keyword analysis.
     */
    private function computeKeywordChartData(): void
    {
        $this->data = [];
        $this->maxKeywordFreq = 0;

        $analysis = $this->profileMatching['keyword_analysis'] ?? null;
        if (!is_array($analysis)) {
            return;
        }

        $resumeKw = is_array($analysis['resume_keywords'] ?? null) ? $analysis['resume_keywords'] : [];
        $jdKw = is_array($analysis['job_description_keywords'] ?? null) ? $analysis['job_description_keywords'] : [];

        if ($resumeKw === [] && $jdKw === []) {
            return;
        }

        $allKeywords = collect($resumeKw)->pluck('keyword')
            ->merge(collect($jdKw)->pluck('keyword'))
            ->filter(fn ($k) => is_string($k) && $k !== '')
            ->unique()
            ->values();

        // Determine max frequency for Y-axis scaling
        $maxResume = (int) (collect($resumeKw)->max('frequency') ?? 0);
        $maxJd = (int) (collect($jdKw)->max('frequency') ?? 0);
        $this->maxKeywordFreq = max($maxResume, $maxJd, 0);
        if ($this->maxKeywordFreq <= 0) {
            $this->maxKeywordFreq = 1;
        }
        // Ensure at least 5 tick points on the Y axis by rounding up to a nice multiple of 5
        $minTicks = 5; // 0..4 gives 5 points
        $rawMax = max($this->maxKeywordFreq, $minTicks - 1);
        $this->yAxisMax = (int) (ceil($rawMax / 5) * 5);
        if ($this->yAxisMax < $minTicks - 1) {
            $this->yAxisMax = $minTicks - 1;
        }

        $resumeByKeyword = collect($resumeKw)->keyBy('keyword');
        $jdByKeyword = collect($jdKw)->keyBy('keyword');

        $this->data = $allKeywords->map(function (string $keyword) use ($resumeByKeyword, $jdByKeyword) {
            $resumeFreq = (int) ($resumeByKeyword[$keyword]['frequency'] ?? 0);
            $jdFreq = (int) ($jdByKeyword[$keyword]['frequency'] ?? 0);

            return [
                'keyword' => $keyword,
                'resume' => $resumeFreq,
                'jd' => $jdFreq,
            ];
        })->all();
    }
}

<?php

use App\Services\AI\RoleAnalysisService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

new class extends Component {
    public App\Models\JobApplication $application;

    public ?array $roleAnalysis = null;
    public bool $isAnalyzingRole = false;
    public int $remainingAnalyses = 0;

    public function mount(App\Models\JobApplication $application): void
    {
        $this->application = $application->loadMissing('documents', 'roleAnalysis');
        $this->roleAnalysis = $this->application->roleAnalysis?->data ?? null;

        // Initialize remaining role analyses
        $service = app(RoleAnalysisService::class);
        $this->remainingAnalyses = $service->getRemainingAnalyses(Auth::user());
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

            // Persist to AIAnalysis model using polymorphic relationship
            $this->application->roleAnalysis()->Create([
                'data' => $result->data,
                'type' => 'role_analysis',
                'provider' => $result->provider,
                'model' => $result->model,
                'prompt_tokens' => $result->promptTokens,
                'completion_tokens' => $result->completionTokens,
                'analyzed_at' => now(),
            ]);

            $this->remainingAnalyses = $service->getRemainingAnalyses(Auth::user());

            Flux::toast(
                text: 'Role analysis completed successfully.',
                heading: 'Analysis Complete',
                variant: 'success',
            );
        } catch (\Throwable $e) {
            Flux::toast(
                text: $e->getMessage(),
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

    public function downloadAnalysis(): StreamedResponse
    {
        if (!$this->roleAnalysis) {
            Flux::toast(
                text: 'No analysis available to download.',
                heading: 'Download Failed',
                variant: 'danger',
            );

            return response()->streamDownload(fn() => '', '');
        }

        $html = view('pdf.role-analysis', [
            'analysis' => $this->roleAnalysis,
            'application' => $this->application,
        ])->render();

        $fileName = str($this->application->job_title)
            ->slug()
            ->append('-role-analysis-')
            ->append(date('Y-m-d'))
            ->append('.pdf')
            ->toString();

        // Generate PDF to a temporary file
        $tempPath = storage_path('app/temp/' . uniqid('pdf_') . '.pdf');

        \Spatie\LaravelPdf\Facades\Pdf::html($html)
            ->format('A4')
            ->withBrowsershot(function (\Spatie\Browsershot\Browsershot $browsershot): void {
                $browsershot->scale(0.8);
                $browsershot->margins(10, 10, 10, 10);
            })
            ->save($tempPath);

        // Stream the file and delete after
        return response()->streamDownload(function () use ($tempPath): void {
            echo file_get_contents($tempPath);
            @unlink($tempPath);
        }, $fileName);
    }
} ?>

<div class="space-y-6">
    @if (empty($application->job_description))
        <flux:heading size="lg">
            {{ __('Role Analysis') }}
        </flux:heading>
        <div
            class="space-y-6 rounded-lg bg-zinc-100 px-6 text-sm text-center dark:bg-zinc-900 py-16"
        >
            <p class="text-zinc-600 dark:text-zinc-400">
                {{ __('Add a job description to run role analysis.') }}
            </p>
        </div>
    @else
        <div class="flex items-center justify-between">
            <flux:heading size="lg">
                {{ __('Role Analysis') }}
            </flux:heading>
            <div class="flex items-center gap-3">
                @if ($roleAnalysis)
                    <flux:button
                        wire:click="downloadAnalysis"
                        variant="primary"
                        icon="arrow-down-tray"
                        size="sm"
                    >
                        {{ __('Download PDF') }}
                    </flux:button>
                @endif

                <flux:button
                    wire:click="analyzeRole"
                    variant="primary"
                    icon-trailing="sparkles"
                    size="sm"
                >
                    @if ($roleAnalysis)
                        {{ __('Regenerate Analysis') }}
                    @else
                        {{ __('Generate Analysis') }}
                    @endif
                </flux:button>
            </div>
        </div>

        @if ($roleAnalysis)
            <div class="space-y-6">
                {{-- Comprehensive Overview --}}
                @if (isset($roleAnalysis['comprehensive_overview']))
                    <flux:card>
                        <flux:heading
                            size="lg"
                            class="mb-4"
                        >
                            Comprehensive Overview
                        </flux:heading>
                        <p
                            class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed mb-4"
                        >
                            {{ $roleAnalysis['comprehensive_overview']['summary'] ?? 'No summary available.' }}
                        </p>

                        @if (isset($roleAnalysis['comprehensive_overview']['actionable_takeaway']))
                            <div
                                class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800"
                            >
                                <div
                                    class="flex items-start gap-3"
                                >
                                    <flux:icon.light-bulb
                                        class="size-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5"
                                    />
                                    <div>
                                        <p
                                            class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1"
                                        >
                                            Actionable
                                            Takeaway
                                        </p>
                                        <p
                                            class="text-sm text-blue-800 dark:text-blue-200"
                                        >
                                            {{ $roleAnalysis['comprehensive_overview']['actionable_takeaway'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </flux:card>
                @endif

                {{-- Keywords Section --}}
                @if (isset($roleAnalysis['keywords']) && count($roleAnalysis['keywords']) > 0)
                    <flux:card>
                        <flux:heading
                            size="lg"
                            class="mb-2"
                        >
                            Keywords
                        </flux:heading>
                        <flux:subheading class="mb-6">
                            List the 10 most relevant
                            keywords and phrases
                            (including variations) that
                            a candidate should emphasize
                            in their CV and cover
                            letter. Use these terms
                            naturally throughout your
                            application materials to
                            increase visibility to
                            Applicant Tracking Systems
                            (ATS) and demonstrate
                            relevance.
                        </flux:subheading>

                        <div class="space-y-4">
                            @foreach ($roleAnalysis['keywords'] as $index => $item)
                                <div class="flex gap-3">
                                    <div
                                        class="flex-shrink-0 w-6 text-center"
                                    >
                                                                <span
                                                                    class="font-semibold text-blue-600 dark:text-blue-400"
                                                                >
                                                                    {{ $index + 1 }}.
                                                                </span>
                                    </div>
                                    <div
                                        class="flex-1 min-w-0"
                                    >
                                        <p
                                            class="font-semibold text-zinc-900 dark:text-zinc-100 mb-1"
                                        >
                                            {{ $item['keyword'] }}
                                        </p>
                                        <p
                                            class="text-sm text-zinc-600 dark:text-zinc-400"
                                        >
                                            {{ $item['explanation'] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div
                            class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800"
                        >
                            <p
                                class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1"
                            >
                                Actionable Takeaway
                            </p>
                            <p
                                class="text-sm text-blue-800 dark:text-blue-200"
                            >
                                Prioritize these
                                keywords throughout your
                                CV and cover letter,
                                tailoring your language
                                to match the job
                                description's
                                terminology.
                            </p>
                        </div>
                    </flux:card>
                @endif

                {{-- Hard Skills and Soft Skills in Two Column Layout --}}
                @if ((isset($roleAnalysis['hard_skills']) && count($roleAnalysis['hard_skills']) > 0) || (isset($roleAnalysis['soft_skills']) && count($roleAnalysis['soft_skills']) > 0))
                    <div
                        class="grid grid-cols-1 lg:grid-cols-2 gap-6"
                    >
                        {{-- Hard Skills --}}
                        @if (isset($roleAnalysis['hard_skills']) && count($roleAnalysis['hard_skills']) > 0)
                            <flux:card>
                                <flux:heading
                                    size="lg"
                                    class="mb-2"
                                >
                                    Hard Skills
                                </flux:heading>
                                <flux:subheading
                                    class="mb-6"
                                >
                                    List up to 5 of the
                                    most critical
                                    technical or
                                    job-specific skills
                                    required for success
                                    in this role.
                                    Highlight these
                                    skills prominently
                                    on your CV,
                                    providing specific
                                    examples of how
                                    you've used them to
                                    achieve results.
                                </flux:subheading>

                                <div class="space-y-5">
                                    @foreach ($roleAnalysis['hard_skills'] as $index => $skill)
                                        <div
                                            class="space-y-2"
                                        >
                                            <div
                                                class="flex items-start gap-2"
                                            >
                                                                        <span
                                                                            class="font-semibold text-blue-600 dark:text-blue-400 flex-shrink-0"
                                                                        >
                                                                            {{ $index + 1 }}.
                                                                        </span>
                                                <div
                                                    class="flex-1"
                                                >
                                                    <flux:heading
                                                        size="sm"
                                                        class="text-zinc-900 dark:text-zinc-100"
                                                    >
                                                        {{ $skill['skill'] }}
                                                    </flux:heading>
                                                </div>
                                            </div>
                                            <p
                                                class="text-sm text-zinc-600 dark:text-zinc-400 ml-6"
                                            >
                                                {{ $skill['description'] }}
                                            </p>
                                            @if (isset($skill['example']))
                                                <div
                                                    class="mt-2 ml-6 p-3 bg-zinc-100 dark:bg-zinc-800 rounded border border-zinc-200 dark:border-zinc-700"
                                                >
                                                    <p
                                                        class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5"
                                                    >
                                                        Example:
                                                    </p>
                                                    <p
                                                        class="text-sm text-zinc-600 dark:text-zinc-400 italic"
                                                    >
                                                        "{{ $skill['example'] }}"
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                <div
                                    class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800"
                                >
                                    <p
                                        class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1"
                                    >
                                        Actionable
                                        Takeaway
                                    </p>
                                    <p
                                        class="text-sm text-blue-800 dark:text-blue-200"
                                    >
                                        Showcase your
                                        technical skills
                                        with specific
                                        examples of how
                                        you've applied
                                        them to solve
                                        problems and
                                        achieve results.
                                    </p>
                                </div>
                            </flux:card>
                        @endif

                        {{-- Soft Skills --}}
                        @if (isset($roleAnalysis['soft_skills']) && count($roleAnalysis['soft_skills']) > 0)
                            <flux:card>
                                <flux:heading
                                    size="lg"
                                    class="mb-2"
                                >
                                    Soft Skills
                                </flux:heading>
                                <flux:subheading
                                    class="mb-6"
                                >
                                    List up to 5 of the
                                    most important
                                    interpersonal and
                                    communication skills
                                    needed for this
                                    position. Showcase
                                    these soft skills
                                    through stories and
                                    examples of how
                                    you've collaborated
                                    effectively with
                                    others or
                                    demonstrated
                                    leadership
                                    qualities.
                                </flux:subheading>

                                <div class="space-y-5">
                                    @foreach ($roleAnalysis['soft_skills'] as $index => $skill)
                                        <div
                                            class="space-y-2"
                                        >
                                            <div
                                                class="flex items-start gap-2"
                                            >
                                                                        <span
                                                                            class="font-semibold text-blue-600 dark:text-blue-400 flex-shrink-0"
                                                                        >
                                                                            {{ $index + 1 }}.
                                                                        </span>
                                                <div
                                                    class="flex-1"
                                                >
                                                    <flux:heading
                                                        size="sm"
                                                        class="text-zinc-900 dark:text-zinc-100"
                                                    >
                                                        {{ $skill['skill'] }}
                                                    </flux:heading>
                                                </div>
                                            </div>
                                            <p
                                                class="text-sm text-zinc-600 dark:text-zinc-400 ml-6"
                                            >
                                                {{ $skill['description'] }}
                                            </p>
                                            @if (isset($skill['example']))
                                                <div
                                                    class="mt-2 ml-6 p-3 bg-zinc-100 dark:bg-zinc-800 rounded border border-zinc-200 dark:border-zinc-700"
                                                >
                                                    <p
                                                        class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5"
                                                    >
                                                        Example:
                                                    </p>
                                                    <p
                                                        class="text-sm text-zinc-600 dark:text-zinc-400 italic"
                                                    >
                                                        "{{ $skill['example'] }}"
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                <div
                                    class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800"
                                >
                                    <p
                                        class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1"
                                    >
                                        Actionable
                                        Takeaway
                                    </p>
                                    <p
                                        class="text-sm text-blue-800 dark:text-blue-200"
                                    >
                                        Weave stories
                                        into your CV and
                                        cover letter
                                        that demonstrate
                                        your ability to
                                        work effectively
                                        with others,
                                        solve problems,
                                        and communicate
                                        clearly.
                                    </p>
                                </div>
                            </flux:card>
                        @endif
                    </div>
                @endif

                {{-- Ideal Candidate Profile and Tailoring Recommendations --}}
                @if (isset($roleAnalysis['ideal_candidate_profile']))
                    <flux:card>
                        <flux:heading
                            size="lg"
                            class="mb-2"
                        >
                            Ideal Candidate Profile and
                            Tailoring Recommendations
                        </flux:heading>
                        <flux:subheading class="mb-6">
                            Summarize the key
                            attributes, experiences, and
                            motivations of an ideal
                            candidate. Provide specific
                            recommendations on how to
                            tailor your CV and cover
                            letter to align with this
                            profile.
                        </flux:subheading>

                        <div class="space-y-6">
                            @if (isset($roleAnalysis['ideal_candidate_profile']['summary']))
                                <div>
                                    <p
                                        class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed"
                                    >
                                        {{ $roleAnalysis['ideal_candidate_profile']['summary'] }}
                                    </p>
                                </div>
                            @endif

                            @if (isset($roleAnalysis['ideal_candidate_profile']['tailoring_recommendations']))
                                <flux:separator
                                    variant="subtle"
                                />

                                <div>
                                    <p
                                        class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-3"
                                    >
                                        Tailoring
                                        Recommendations:
                                    </p>

                                    @if (isset($roleAnalysis['ideal_candidate_profile']['tailoring_recommendations']['cv']))
                                        <div
                                            class="mb-4"
                                        >
                                            <p
                                                class="text-sm font-medium text-zinc-900 dark:text-zinc-100 mb-2"
                                            >
                                                <flux:icon.document-text
                                                    class="inline size-4"
                                                />
                                                CV:
                                            </p>
                                            <p
                                                class="text-sm text-zinc-600 dark:text-zinc-400 ml-6"
                                            >
                                                {{ $roleAnalysis['ideal_candidate_profile']['tailoring_recommendations']['cv'] }}
                                            </p>
                                        </div>
                                    @endif

                                    @if (isset($roleAnalysis['ideal_candidate_profile']['tailoring_recommendations']['cover_letter']))
                                        <div>
                                            <p
                                                class="text-sm font-medium text-zinc-900 dark:text-zinc-100 mb-2"
                                            >
                                                <flux:icon.document-text
                                                    class="inline size-4"
                                                />
                                                Cover
                                                Letter:
                                            </p>
                                            <p
                                                class="text-sm text-zinc-600 dark:text-zinc-400 ml-6"
                                            >
                                                {{ $roleAnalysis['ideal_candidate_profile']['tailoring_recommendations']['cover_letter'] }}
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <div
                                    class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800"
                                >
                                    <p
                                        class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1"
                                    >
                                        Actionable
                                        Takeaway
                                    </p>
                                    <p
                                        class="text-sm text-blue-800 dark:text-blue-200"
                                    >
                                        Position
                                        yourself as a
                                        well-rounded
                                        candidate who is
                                        not only
                                        technically
                                        skilled but also
                                        aligned with the
                                        role's
                                        requirements and
                                        eager to
                                        contribute to
                                        the
                                        organization's
                                        mission.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </flux:card>
                @endif
            </div>
        @endif
    @endif
</div>


<?php

use App\Enums\DocumentType;
use App\Jobs\ProcessProfileMatching;
use App\Services\AI\RateLimiting\AnalysisRateLimiter;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    public App\Models\JobApplication $application;
    public ?array $profileMatching = null;
    public bool $isGenerating = false;

    // Chart data
    public array $data = [];
    public int $maxKeywordFreq = 0;
    public int $yAxisMax = 0;

    public function mount(App\Models\JobApplication $application): void
    {
        $this->application = $application->loadMissing('documents');
        $this->profileMatching = $this->application->profile_matching ?? null;
        $this->computeKeywordChartData();
    }

    public function generateProfileMatching(): void
    {
        $descHtml = (string)($this->application->job_description ?? '');
        $desc = mb_trim(strip_tags($descHtml));
        $desc = mb_substr($desc, 0, 6000);
        if (mb_strlen($desc) < 60) {
            Flux::toast(text: 'Please add a more complete job description to run profile matching.', heading: 'Job Description Required', variant: 'warning');
            return;
        }

        $cv = $this->application->documents->first(fn($d) => $d->type?->value === DocumentType::CurriculumVitae->value);
        if (!$cv) {
            Flux::toast(text: 'A comprehensive profile matching analysis cannot be performed without a CV. Please upload your CV first.', heading: 'CV Required', variant: 'warning');
            return;
        }

        $limiter = app(AnalysisRateLimiter::class);
        try {
            $limiter->check(Auth::user(), 'role_analysis');
        } catch (\Throwable $e) {
            Flux::toast(text: 'Daily limit reached for AI generation. Try again tomorrow.', heading: 'Limit Reached', variant: 'warning');
            return;
        }

        $systemPrompt = mb_trim((string)view('prompts.profile-matching')->render());
        if ($systemPrompt === '') {
            $systemPrompt = 'Analyze the job description and CV to provide a comprehensive profile matching analysis with scores, strengths, gaps, keyword analysis, skills analysis, experience match, education match, and actionable suggestions.';
        }

        $this->isGenerating = true;
        $this->profileMatching = null; // Clear old data to prevent false success message

        // Dispatch job to process in background
        ProcessProfileMatching::dispatch($this->application, $cv, $desc, $systemPrompt);

        $limiter->hit(Auth::user(), 'role_analysis');

        Flux::toast(
            text: 'Profile matching started. This may take 2-3 minutes. The page will update automatically when complete.',
            heading: 'Processing...',
            variant: 'info'
        );
    }

    #[On('refresh-profile')]
    public function refreshProfile(): void
    {
        // Refresh the application model from database
        $this->application = $this->application->fresh();
        $newData = $this->application->profile_matching ?? null;

        // Only show success if we actually got NEW data (wasn't null before)
        if ($newData && !$this->profileMatching && $this->isGenerating) {
            $this->isGenerating = false;
            $this->profileMatching = $newData;
            $this->computeKeywordChartData();
            Flux::toast(text: 'Profile matching completed successfully.', heading: 'Done', variant: 'success');
        } else {
            $this->profileMatching = $newData;
            $this->computeKeywordChartData();
        }
    }

    private function computeKeywordChartData(): void
    {
        $this->data = [];
        $this->maxKeywordFreq = 0;
        $this->yAxisMax = 0;

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
            ->filter(fn($k) => is_string($k) && $k !== '')
            ->unique()
            ->values();

        $maxResume = (int)(collect($resumeKw)->max('frequency') ?? 0);
        $maxJd = (int)(collect($jdKw)->max('frequency') ?? 0);
        $this->maxKeywordFreq = max($maxResume, $maxJd, 0);
        if ($this->maxKeywordFreq <= 0) {
            $this->maxKeywordFreq = 1;
        }
        $rawMax = max($this->maxKeywordFreq, 4);
        $this->yAxisMax = (int)(ceil($rawMax / 5) * 5);
        if ($this->yAxisMax < 4) {
            $this->yAxisMax = 4;
        }

        $resumeByKeyword = collect($resumeKw)->keyBy('keyword');
        $jdByKeyword = collect($jdKw)->keyBy('keyword');

        $this->data = $allKeywords->map(function (string $keyword) use ($resumeByKeyword, $jdByKeyword) {
            $resumeFreq = (int)($resumeByKeyword[$keyword]['frequency'] ?? 0);
            $jdFreq = (int)($jdByKeyword[$keyword]['frequency'] ?? 0);
            return ['keyword' => $keyword, 'resume' => $resumeFreq, 'jd' => $jdFreq];
        })->all();
    }
} ?>

<div class="space-y-6" @if($isGenerating) wire:poll.5s="refreshProfile" @endif>
    <div class="flex items-center justify-between">
        <flux:heading size="lg">
            {{ __('Profile Matching') }}
        </flux:heading>
        <flux:button
            variant="primary"
            icon="sparkles"
            wire:click="generateProfileMatching"
        >
            {{ $profileMatching ? __('Regenerate') : __('Generate Analysis') }}
        </flux:button>
    </div>

    @if ($profileMatching)
        {{-- Overall Match Score --}}
        <flux:card>
            <div
                class="flex items-center justify-between"
            >
                <flux:heading size="lg">
                    {{ __('Overall Match Score') }}
                </flux:heading>
                <div class="flex flex-col items-center">
                    <div class="flex items-baseline">
                                                <span
                                                    class="text-3xl font-bold text-blue-600 dark:text-blue-400"
                                                >
                                                    {{ $profileMatching['overall_match_score'] ?? 0 }}
                                                </span>
                        <span
                            class="text-xl text-zinc-500 dark:text-zinc-400"
                        >
                                                    %
                                                </span>
                    </div>
                </div>
            </div>
            <p
                class="mt-4 text-sm text-zinc-600 dark:text-zinc-400"
            >
                {{ $profileMatching['summary'] ?? __('Comprehensive analysis of your profile against the job requirements.') }}
            </p>
        </flux:card>

        {{-- Strengths and Gaps --}}
        <div
            class="grid grid-cols-1 lg:grid-cols-2 gap-6"
        >
            {{-- Strengths --}}
            <flux:card>
                <div
                    class="flex items-center gap-2 mb-4"
                >
                    <flux:icon.check-circle
                        class="size-5 text-green-600 dark:text-green-400"
                    />
                    <flux:heading
                        size="lg"
                        class="text-green-700 dark:text-green-300"
                    >
                        {{ __('Strengths') }}
                    </flux:heading>
                </div>
                @if (! empty($profileMatching['strengths']))
                    <div class="space-y-4">
                        @foreach ($profileMatching['strengths'] as $strength)
                            <div
                                class="border-l-4 border-green-500 pl-4"
                            >
                                <h4
                                    class="font-semibold text-zinc-900 dark:text-zinc-100"
                                >
                                    {{ $strength['title'] ?? '' }}
                                </h4>
                                <p
                                    class="text-sm text-zinc-600 dark:text-zinc-400 mt-1"
                                >
                                    {{ $strength['description'] ?? '' }}
                                </p>
                                @if (! empty($strength['examples']))
                                    <ul
                                        class="mt-2 text-xs text-zinc-500 dark:text-zinc-500 space-y-1"
                                    >
                                        @foreach ($strength['examples'] as $example)
                                            <li>
                                                •
                                                {{ $example }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-zinc-500">
                        {{ __('No strengths identified') }}
                    </p>
                @endif
            </flux:card>

            {{-- Gaps --}}
            <flux:card>
                <div
                    class="flex items-center gap-2 mb-4"
                >
                    <flux:icon.exclamation-triangle
                        class="size-5 text-amber-600 dark:text-amber-400"
                    />
                    <flux:heading
                        size="lg"
                        class="text-amber-700 dark:text-amber-300"
                    >
                        {{ __('Gaps') }}
                    </flux:heading>
                </div>
                @if (! empty($profileMatching['gaps']))
                    <div class="space-y-4">
                        @foreach ($profileMatching['gaps'] as $gap)
                            @php
                                $impactColor = match ($gap['impact'] ?? 'medium') {
                                    'high' => 'red',
                                    'low' => 'yellow',
                                    default => 'amber',
                                };
                            @endphp

                            <div
                                class="border-l-4 border-{{ $impactColor }}-500 pl-4"
                            >
                                <div
                                    class="flex items-start justify-between"
                                >
                                    <h4
                                        class="font-semibold text-zinc-900 dark:text-zinc-100"
                                    >
                                        {{ $gap['title'] ?? '' }}
                                    </h4>
                                    <flux:badge
                                        color="{{ $impactColor }}"
                                        size="sm"
                                    >
                                        {{ ucfirst($gap['impact'] ?? 'medium') }}
                                    </flux:badge>
                                </div>
                                <p
                                    class="text-sm text-zinc-600 dark:text-zinc-400 mt-1"
                                >
                                    {{ $gap['description'] ?? '' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-zinc-500">
                        {{ __('No critical gaps identified') }}
                    </p>
                @endif
            </flux:card>
        </div>

        {{-- Keyword Analysis - Line Chart --}}
        @if (! empty($profileMatching['keyword_analysis']))
            <flux:card>
                <flux:heading size="lg" class="mb-4">
                    {{ __('Keyword Analysis') }}
                </flux:heading>

                <flux:chart wire:model="data">
                    <flux:chart.viewport class="min-h-[20rem]">
                        <flux:chart.svg>
                            <flux:chart.line field="resume" class="text-blue-500"
                                             curve="none"/>
                            <flux:chart.point field="resume" class="text-blue-500" r="5"
                                              stroke-width="2"/>
                            <flux:chart.line field="jd" class="text-violet-500"
                                             curve="none"/>
                            <flux:chart.point field="jd" class="text-violet-500" r="5"
                                              stroke-width="2"/>

                            <flux:chart.axis axis="x" field="keyword">
                                <flux:chart.axis.tick/>
                                <flux:chart.axis.line/>
                            </flux:chart.axis>

                            <flux:chart.axis axis="y" tick-start="0"
                                             tick-end="{{ $yAxisMax }}" :format="[
                                                        'minimumFractionDigits' => 0,
                                                        'maximumFractionDigits' => 0,
                                                    ]">
                                <flux:chart.axis.grid/>
                                <flux:chart.axis.tick/>
                            </flux:chart.axis>

                            {{-- Tooltips removed as requested --}}
                        </flux:chart.svg>
                    </flux:chart.viewport>

                    <div class="flex justify-center gap-4 pt-4">
                        <flux:chart.legend label="{{ __('Resume') }}">
                            <flux:chart.legend.indicator class="bg-blue-400"/>
                        </flux:chart.legend>

                        <flux:chart.legend label="{{ __('Job Description') }}">
                            <flux:chart.legend.indicator class="bg-violet-400"/>
                        </flux:chart.legend>
                    </div>
                </flux:chart>

                <flux:text class="mt-2 text-xs text-zinc-500">
                    {{ __('Frequencies normalized to percentage of the highest keyword count.') }}
                </flux:text>

                @if(false)
                    @php
                        $resumeKw = $profileMatching['keyword_analysis']['resume_keywords'] ?? [];
                        $jdKw = $profileMatching['keyword_analysis']['job_description_keywords'] ?? [];
                        $allKeywords = collect($resumeKw)
                            ->pluck('keyword')
                            ->merge(collect($jdKw)->pluck('keyword'))
                            ->unique()
                            ->values()
                            ->all();
                        $maxFreq = max(collect($resumeKw)->max('frequency') ?? 1, collect($jdKw)->max('frequency') ?? 1);
                    @endphp

                    <div class="h-64 relative mb-8">
                        {{-- Y-axis labels --}}
                        <div
                            class="absolute left-0 top-0 bottom-0 w-8 flex flex-col justify-between text-xs text-zinc-500 dark:text-zinc-400 pr-2 text-right"
                        >
                            <span>{{ $maxFreq }}</span>
                            <span>
                                                    {{ round($maxFreq * 0.75) }}
                                                </span>
                            <span>
                                                    {{ round($maxFreq * 0.5) }}
                                                </span>
                            <span>
                                                    {{ round($maxFreq * 0.25) }}
                                                </span>
                            <span>0</span>
                        </div>

                        {{-- Chart area --}}
                        <div
                            class="ml-10 h-full border-l-2 border-b-2 border-zinc-300 dark:border-zinc-700 relative"
                        >
                            @php
                                $chartWidth = 100;
                                $chartHeight = 100;
                                $numKeywords = count($allKeywords);

                                // Build Resume line points
                                $resumePoints = [];
                                foreach ($allKeywords as $idx => $keyword) {
                                    $resumeItem = collect($resumeKw)->firstWhere('keyword', $keyword);
                                    $freq = $resumeItem['frequency'] ?? 0;
                                    $x = $numKeywords > 1 ? ($idx / ($numKeywords - 1)) * $chartWidth : 50;
                                    $y = $maxFreq > 0 ? $chartHeight - ($freq / $maxFreq) * $chartHeight : $chartHeight;
                                    $resumePoints[] = "$x $y";
                                }

                                // Build JD line points
                                $jdPoints = [];
                                foreach ($allKeywords as $idx => $keyword) {
                                    $jdItem = collect($jdKw)->firstWhere('keyword', $keyword);
                                    $freq = $jdItem['frequency'] ?? 0;
                                    $x = $numKeywords > 1 ? ($idx / ($numKeywords - 1)) * $chartWidth : 50;
                                    $y = $maxFreq > 0 ? $chartHeight - ($freq / $maxFreq) * $chartHeight : $chartHeight;
                                    $jdPoints[] = "$x $y";
                                }
                            @endphp

                            <svg
                                class="w-full h-full"
                                viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}"
                                preserveAspectRatio="none"
                            >
                                {{-- Resume line (blue) --}}
                                @if (count($resumePoints) > 0)
                                    <polyline
                                        points="{{ implode(' ', $resumePoints) }}"
                                        fill="none"
                                        stroke="rgb(37, 99, 235)"
                                        stroke-width="0.5"
                                        vector-effect="non-scaling-stroke"
                                    />

                                    {{-- Add dots at each point --}}
                                    @foreach ($resumePoints as $point)
                                        @php
                                            [$px, $py] = explode(' ', $point);
                                        @endphp

                                        <circle
                                            cx="{{ $px }}"
                                            cy="{{ $py }}"
                                            r="1"
                                            fill="rgb(37, 99, 235)"
                                        />
                                    @endforeach
                                @endif

                                {{-- JD line (violet) --}}
                                @if (count($jdPoints) > 0)
                                    <polyline
                                        points="{{ implode(' ', $jdPoints) }}"
                                        fill="none"
                                        stroke="rgb(139, 92, 246)"
                                        stroke-width="0.5"
                                        vector-effect="non-scaling-stroke"
                                    />

                                    {{-- Add dots at each point --}}
                                    @foreach ($jdPoints as $point)
                                        @php
                                            [$px, $py] = explode(' ', $point);
                                        @endphp

                                        <circle
                                            cx="{{ $px }}"
                                            cy="{{ $py }}"
                                            r="1"
                                            fill="rgb(139, 92, 246)"
                                        />
                                    @endforeach
                                @endif
                            </svg>

                            {{-- X-axis labels --}}
                            <div
                                class="absolute -bottom-8 left-0 right-0 flex justify-between text-xs text-zinc-500 dark:text-zinc-400 px-1"
                            >
                                @foreach (array_slice($allKeywords, 0, min(10, count($allKeywords))) as $keyword)
                                    <span
                                        class="truncate max-w-[60px]"
                                    >
                                                            {{ $keyword }}
                                                        </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-center gap-6 mt-8"
                    >
                        <div
                            class="flex items-center gap-2"
                        >
                            <div
                                class="w-4 h-1 bg-blue-600"
                            ></div>
                            <span
                                class="text-sm text-zinc-600 dark:text-zinc-400"
                            >
                                                    {{ __('Resume Keywords') }}
                                                </span>
                        </div>
                        <div
                            class="flex items-center gap-2"
                        >
                            <div
                                class="w-4 h-1 bg-violet-600"
                            ></div>
                            <span
                                class="text-sm text-zinc-600 dark:text-zinc-400"
                            >
                                                    {{ __('JD Keywords') }}
                                                </span>
                        </div>
                    </div>
                @endif
            </flux:card>
        @endif

        {{-- Skills Analysis Table --}}
        @if (! empty($profileMatching['skills_analysis']))
            <flux:card>
                <flux:heading size="lg" class="mb-4">
                    {{ __('Skills Analysis') }}
                </flux:heading>
                @php
                    $skills = collect($profileMatching['skills_analysis'])
                        ->map(function ($item) {
                            $resume = (int)($item['resume_frequency'] ?? 0);
                            $jd = (int)($item['jd_frequency'] ?? 0);
                            return [
                                'skill' => (string)($item['skill'] ?? ''),
                                'resume_frequency' => $resume,
                                'jd_frequency' => $jd,
                                'resume_coverage' => $resume > 0 ? 100 : 0,
                                'jd_coverage' => 100,
                                'status' => $resume > 0 ? 'matched' : 'missing',
                            ];
                        })
                        ->values()
                        ->all();
                @endphp
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>
                            {{ __('Skill/Keyword') }}
                        </flux:table.column>
                        <flux:table.column>
                            {{ __('Resume Coverage') }}
                        </flux:table.column>
                        <flux:table.column>
                            {{ __('JD Coverage') }}
                        </flux:table.column>
                        <flux:table.column>
                            {{ __('Resume Freq') }}
                        </flux:table.column>
                        <flux:table.column>
                            {{ __('JD Freq') }}
                        </flux:table.column>
                        <flux:table.column>
                            {{ __('Status') }}
                        </flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach ($skills as $skill)
                            <flux:table.row>
                                <flux:table.cell
                                    class="font-medium"
                                >
                                    {{ $skill['skill'] ?? '' }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $skill['resume_coverage'] ?? 0 }}%
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $skill['jd_coverage'] ?? 0 }}%
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $skill['resume_frequency'] ?? 0 }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $skill['jd_frequency'] ?? 0 }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    @php
                                        $statusColor = ($skill['status'] ?? 'missing') === 'matched' ? 'green' : 'red';
                                    @endphp

                                    <flux:badge
                                        color="{{ $statusColor }}"
                                        size="sm"
                                    >
                                        {{ ucfirst($skill['status'] ?? 'missing') }}
                                    </flux:badge>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        @endif

        {{-- Experience Match --}}
        @if (! empty($profileMatching['experience_match']))
            <flux:card>
                @php
                    $expScore = (int) ($profileMatching['experience_match']['score_percent'] ?? null);
                    $hasExpScore = $expScore > 0 || $expScore === 0;
                    $expScoreSafe = max(0, min(100, $expScore));
                @endphp
                <flux:heading size="lg" class="mb-4 flex items-center justify-between">
                    <span>{{ __('Experience Match') }}</span>
                    @if ($hasExpScore)
                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $expScoreSafe }}%</span>
                    @endif
                </flux:heading>
                <p class="text-sm text-zinc-700 dark:text-zinc-300 mb-4">
                    {{ $profileMatching['experience_match']['overview'] ?? '' }}
                </p>
                @if (! empty($profileMatching['experience_match']['suggestions']))
                    <div>
                        <h4
                            class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2"
                        >
                            {{ __('Suggestions') }}
                        </h4>
                        <ul
                            class="text-sm text-zinc-600 dark:text-zinc-400 space-y-1"
                        >
                            @foreach ($profileMatching['experience_match']['suggestions'] as $sug)
                                <li>• {{ $sug }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </flux:card>
        @endif

        {{-- Education & Certifications --}}
        @if (! empty($profileMatching['education_certifications']))
            <flux:card>
                @php
                    $eduScore = (int) ($profileMatching['education_certifications']['score_percent'] ?? null);
                    $hasEduScore = $eduScore > 0 || $eduScore === 0;
                    $eduScoreSafe = max(0, min(100, $eduScore));
                @endphp
                <flux:heading size="lg" class="mb-4 flex items-center justify-between">
                    <span>{{ __('Education & Certifications') }}</span>
                    @if ($hasEduScore)
                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $eduScoreSafe }}%</span>
                    @endif
                </flux:heading>
                <p class="text-sm text-zinc-700 dark:text-zinc-300 mb-4">
                    {{ $profileMatching['education_certifications']['overview'] ?? '' }}
                </p>
                @if (! empty($profileMatching['education_certifications']['suggestions']))
                    <div>
                        <h4
                            class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2"
                        >
                            {{ __('Suggestions') }}
                        </h4>
                        <ul
                            class="text-sm text-zinc-600 dark:text-zinc-400 space-y-1"
                        >
                            @foreach ($profileMatching['education_certifications']['suggestions'] as $sug)
                                <li>• {{ $sug }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </flux:card>
        @endif

        {{-- Technical Skills --}}
        @if (! empty($profileMatching['technical_skills']))
            <flux:card>
                @php
                    // Prefer model-provided score; fallback to coverage ratio from skills table
                    $techScore = $profileMatching['technical_skills']['score_percent'] ?? null;
                    if ($techScore === null) {
                        $total = collect($profileMatching['skills_analysis'] ?? [])->filter(fn($s) => (int)($s['jd_frequency'] ?? 0) > 0)->count();
                        $matched = collect($profileMatching['skills_analysis'] ?? [])->filter(fn($s) => (int)($s['jd_frequency'] ?? 0) > 0 && (int)($s['resume_frequency'] ?? 0) > 0)->count();
                        $techScore = $total > 0 ? (int) round(($matched / $total) * 100) : null;
                    }
                    $hasTechScore = $techScore !== null;
                    $techScoreSafe = $hasTechScore ? max(0, min(100, (int) $techScore)) : null;
                @endphp
                <flux:heading size="lg" class="mb-4 flex items-center justify-between">
                    <span>{{ __('Technical Skills') }}</span>
                    @if ($hasTechScore)
                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $techScoreSafe }}%</span>
                    @endif
                </flux:heading>
                <p class="text-sm text-zinc-700 dark:text-zinc-300 mb-4">
                    {{ $profileMatching['technical_skills']['overview'] ?? '' }}
                </p>
                @if (! empty($profileMatching['technical_skills']['suggestions']))
                    <div>
                        <h4
                            class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2"
                        >
                            {{ __('Suggestions') }}
                        </h4>
                        <ul
                            class="text-sm text-zinc-600 dark:text-zinc-400 space-y-1"
                        >
                            @foreach ($profileMatching['technical_skills']['suggestions'] as $sug)
                                <li>• {{ $sug }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </flux:card>
        @endif

        {{-- Soft Skills --}}
        @if (! empty($profileMatching['soft_skills']))
            <flux:card>
                @php
                    $softScore = (int) ($profileMatching['soft_skills']['score_percent'] ?? null);
                    $hasSoftScore = $softScore > 0 || $softScore === 0;
                    $softScoreSafe = max(0, min(100, $softScore));
                @endphp
                <flux:heading size="lg" class="mb-4 flex items-center justify-between">
                    <span>{{ __('Soft Skills') }}</span>
                    @if ($hasSoftScore)
                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $softScoreSafe }}%</span>
                    @endif
                </flux:heading>
                <p class="text-sm text-zinc-700 dark:text-zinc-300 mb-4">
                    {{ $profileMatching['soft_skills']['overview'] ?? '' }}
                </p>
                @if (! empty($profileMatching['soft_skills']['suggestions']))
                    <div>
                        <h4
                            class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2"
                        >
                            {{ __('Suggestions') }}
                        </h4>
                        <ul
                            class="text-sm text-zinc-600 dark:text-zinc-400 space-y-1"
                        >
                            @foreach ($profileMatching['soft_skills']['suggestions'] as $sug)
                                <li>• {{ $sug }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </flux:card>
        @endif

        {{-- Priority Recommendations --}}
        @if (! empty($profileMatching['priority_recommendations']))
            <flux:card
                class="bg-blue-50 dark:bg-blue-950/20 border-2 border-blue-200 dark:border-blue-800"
            >
                <div
                    class="flex items-center gap-2 mb-4"
                >
                    <flux:icon.light-bulb
                        class="size-6 text-blue-600 dark:text-blue-400"
                    />
                    <flux:heading
                        size="lg"
                        class="text-blue-900 dark:text-blue-100"
                    >
                        {{ __('Priority Recommendations') }}
                    </flux:heading>
                </div>
                <div class="space-y-3">
                    @php
                        $recs = array_values((array) ($profileMatching['priority_recommendations'] ?? []));
                    @endphp
                    @foreach ($recs as $idx => $rec)
                        @break($idx >= 10)
                        @php
                            $priorityColor = match ($rec['priority'] ?? 'medium') {
                                'high' => 'red',
                                'low' => 'yellow',
                                default => 'amber',
                            };
                        @endphp

                        <div
                            class="flex items-start gap-3 p-3 bg-white dark:bg-zinc-900 rounded border border-blue-200 dark:border-blue-800"
                        >
                            <flux:badge
                                color="{{ $priorityColor }}"
                                size="sm"
                                class="mt-0.5"
                            >
                                {{ ucfirst($rec['priority'] ?? 'medium') }}
                            </flux:badge>
                            <p
                                class="flex-1 text-sm text-blue-900 dark:text-blue-100"
                            >
                                {{ $rec['suggestion'] ?? '' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </flux:card>
        @endif
    @else
        <flux:card class="text-center py-12">
            <flux:icon.sparkles
                class="size-12 text-zinc-400 dark:text-zinc-600 mx-auto mb-4"
            />
            <flux:heading size="lg" class="mb-2">
                {{ __('No Profile Matching Analysis Yet') }}
            </flux:heading>
            <p
                class="text-sm text-zinc-600 dark:text-zinc-400 mb-4"
            >
                {{ __('Click the "Generate Analysis" button to create a comprehensive profile matching analysis.') }}
            </p>
        </flux:card>
    @endif
</div>


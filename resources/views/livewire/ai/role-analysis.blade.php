<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    {{-- Breadcrumbs --}}
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('dashboard') }}">
            {{ __('Home') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('Role Analysis') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">AI Role Analysis</flux:heading>
            <flux:subheading class="mt-1">
                Analyze job descriptions to identify key requirements and optimize your application strategy
            </flux:subheading>
        </div>
        @if (!$analysis && !$isAnalyzing)
            <div class="flex items-center gap-3">
                <flux:badge color="blue" size="lg">
                    <flux:icon.sparkles class="size-4" />
                    {{ $remainingAnalyses }} remaining today
                </flux:badge>
            </div>
        @endif
    </div>

    {{-- Input Form or Analysis Results --}}
    @if (!$analysis && !$isAnalyzing)
        {{-- Input Form --}}
        <div x-data="{ charCount: {{ strlen($jobDescription) }} }">
            <form wire:submit="analyzeRole" class="space-y-6">
                <flux:field>
                    <flux:label>Job Description</flux:label>
                    <flux:textarea
                        wire:model="jobDescription"
                        x-on:input="charCount = $el.value.length"
                        rows="14"
                        placeholder="Paste the complete job description here...

Example:
About the Company:
[Company background]

About the Role:
We're seeking a Senior Software Engineer to join our platform team...

Requirements:
- 5+ years of experience with...
- Strong proficiency in...

Responsibilities:
- Design and develop...
- Collaborate with..."
                        :disabled="$isAnalyzing"
                        class="font-mono text-sm"
                    />
                    <flux:error name="jobDescription" />
                </flux:field>

                <div x-show="charCount >= 100" class="flex items-center justify-end">
                    <flux:button
                        type="submit"
                        variant="primary"
                        icon:trailing="sparkles"
                        wire:loading.attr="disabled"
                        wire:target="analyzeRole"
                    >
                        <span wire:loading.remove wire:target="analyzeRole">
                            Analyze Role
                        </span>
                        <span wire:loading wire:target="analyzeRole" class="flex items-center gap-2">
                            <flux:icon.arrow-path class="animate-spin size-5" />
                            Analyzing...
                        </span>
                    </flux:button>
                </div>
            </form>
        </div>
    @endif

    {{-- Analysis Results --}}
    @if ($analysis)
        <div class="space-y-6">
            {{-- Action Bar --}}
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <flux:badge color="blue" size="lg" class="self-start">
                    <flux:icon.sparkles class="size-4" />
                    {{ $remainingAnalyses }} remaining today
                </flux:badge>

                <div class="flex items-center gap-3">
                    <flux:button
                        wire:click="downloadAnalysis"
                        variant="primary"
                        icon="arrow-down-tray"
                    >
                        Download PDF
                    </flux:button>
                    <flux:button
                        wire:click="clearAnalysis"
                        variant="outline"
                        icon="arrow-path"
                    >
                        New Analysis
                    </flux:button>
                </div>
            </div>

            {{-- Comprehensive Overview --}}
            @if (isset($analysis['comprehensive_overview']))
                <flux:card>
                    <flux:heading size="lg" class="mb-4">Comprehensive Overview</flux:heading>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed mb-4">
                        {{ $analysis['comprehensive_overview']['summary'] ?? 'No summary available.' }}
                    </p>

                    @if (isset($analysis['comprehensive_overview']['actionable_takeaway']))
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="flex items-start gap-3">
                                <flux:icon.light-bulb class="size-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                                <div>
                                    <p class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">
                                        Actionable Takeaway
                                    </p>
                                    <p class="text-sm text-blue-800 dark:text-blue-200">
                                        {{ $analysis['comprehensive_overview']['actionable_takeaway'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </flux:card>
            @endif

            {{-- Keywords Section --}}
            @if (isset($analysis['keywords']) && count($analysis['keywords']) > 0)
                <flux:card>
                    <flux:heading size="lg" class="mb-2">Keywords</flux:heading>
                    <flux:subheading class="mb-6">
                        List the 10 most relevant keywords and phrases (including variations) that a candidate should emphasize in their CV and cover letter. Use these terms naturally throughout your application materials to increase visibility to Applicant Tracking Systems (ATS) and demonstrate relevance.
                    </flux:subheading>

                    <div class="space-y-4">
                        @foreach ($analysis['keywords'] as $index => $item)
                            <div class="flex gap-3">
                                <div class="flex-shrink-0 w-6 text-center">
                                    <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $index + 1 }}.</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-zinc-900 dark:text-zinc-100 mb-1">
                                        {{ $item['keyword'] }}
                                    </p>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $item['explanation'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <p class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">
                            Actionable Takeaway
                        </p>
                        <p class="text-sm text-blue-800 dark:text-blue-200">
                            Prioritize these keywords throughout your CV and cover letter, tailoring your language to match the job description's terminology.
                        </p>
                    </div>
                </flux:card>
            @endif

            {{-- Hard Skills and Soft Skills in Two Column Layout --}}
            @if ((isset($analysis['hard_skills']) && count($analysis['hard_skills']) > 0) || (isset($analysis['soft_skills']) && count($analysis['soft_skills']) > 0))
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Hard Skills --}}
                    @if (isset($analysis['hard_skills']) && count($analysis['hard_skills']) > 0)
                        <flux:card>
                            <flux:heading size="lg" class="mb-2">Hard Skills</flux:heading>
                            <flux:subheading class="mb-6">
                                List up to 5 of the most critical technical or job-specific skills required for success in this role. Highlight these skills prominently on your CV, providing specific examples of how you've used them to achieve results.
                            </flux:subheading>

                            <div class="space-y-5">
                                @foreach ($analysis['hard_skills'] as $index => $skill)
                                    <div class="space-y-2">
                                        <div class="flex items-start gap-2">
                                            <span class="font-semibold text-blue-600 dark:text-blue-400 flex-shrink-0">{{ $index + 1 }}.</span>
                                            <div class="flex-1">
                                                <flux:heading size="sm" class="text-zinc-900 dark:text-zinc-100">
                                                    {{ $skill['skill'] }}
                                                </flux:heading>
                                            </div>
                                        </div>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400 ml-6">
                                            {{ $skill['description'] }}
                                        </p>
                                        @if (isset($skill['example']))
                                            <div class="mt-2 ml-6 p-3 bg-zinc-100 dark:bg-zinc-800 rounded border border-zinc-200 dark:border-zinc-700">
                                                <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
                                                    Example:
                                                </p>
                                                <p class="text-sm text-zinc-600 dark:text-zinc-400 italic">
                                                    "{{ $skill['example'] }}"
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <p class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">
                                    Actionable Takeaway
                                </p>
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    Showcase your technical skills with specific examples of how you've applied them to solve problems and achieve results.
                                </p>
                            </div>
                        </flux:card>
                    @endif

                    {{-- Soft Skills --}}
                    @if (isset($analysis['soft_skills']) && count($analysis['soft_skills']) > 0)
                        <flux:card>
                            <flux:heading size="lg" class="mb-2">Soft Skills</flux:heading>
                            <flux:subheading class="mb-6">
                                List up to 5 of the most important interpersonal and communication skills needed for this position. Showcase these soft skills through stories and examples of how you've collaborated effectively with others or demonstrated leadership qualities.
                            </flux:subheading>

                            <div class="space-y-5">
                                @foreach ($analysis['soft_skills'] as $index => $skill)
                                    <div class="space-y-2">
                                        <div class="flex items-start gap-2">
                                            <span class="font-semibold text-blue-600 dark:text-blue-400 flex-shrink-0">{{ $index + 1 }}.</span>
                                            <div class="flex-1">
                                                <flux:heading size="sm" class="text-zinc-900 dark:text-zinc-100">
                                                    {{ $skill['skill'] }}
                                                </flux:heading>
                                            </div>
                                        </div>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400 ml-6">
                                            {{ $skill['description'] }}
                                        </p>
                                        @if (isset($skill['example']))
                                            <div class="mt-2 ml-6 p-3 bg-zinc-100 dark:bg-zinc-800 rounded border border-zinc-200 dark:border-zinc-700">
                                                <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
                                                    Example:
                                                </p>
                                                <p class="text-sm text-zinc-600 dark:text-zinc-400 italic">
                                                    "{{ $skill['example'] }}"
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <p class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">
                                    Actionable Takeaway
                                </p>
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    Weave stories into your CV and cover letter that demonstrate your ability to work effectively with others, solve problems, and communicate clearly.
                                </p>
                            </div>
                        </flux:card>
                    @endif
                </div>
            @endif

            {{-- Ideal Candidate Profile and Tailoring Recommendations --}}
            @if (isset($analysis['ideal_candidate_profile']))
                <flux:card>
                    <flux:heading size="lg" class="mb-2">Ideal Candidate Profile and Tailoring Recommendations</flux:heading>
                    <flux:subheading class="mb-6">
                        Summarize the key attributes, experiences, and motivations of an ideal candidate. Provide specific recommendations on how to tailor your CV and cover letter to align with this profile.
                    </flux:subheading>

                    <div class="space-y-6">
                        @if (isset($analysis['ideal_candidate_profile']['summary']))
                            <div>
                                <p class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed">
                                    {{ $analysis['ideal_candidate_profile']['summary'] }}
                                </p>
                            </div>
                        @endif

                        @if (isset($analysis['ideal_candidate_profile']['tailoring_recommendations']))
                            <flux:separator variant="subtle" />

                            <div>
                                <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-3">
                                    Tailoring Recommendations:
                                </p>

                                @if (isset($analysis['ideal_candidate_profile']['tailoring_recommendations']['cv']))
                                    <div class="mb-4">
                                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                                            <flux:icon.document-text class="inline size-4" />
                                            CV:
                                        </p>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400 ml-6">
                                            {{ $analysis['ideal_candidate_profile']['tailoring_recommendations']['cv'] }}
                                        </p>
                                    </div>
                                @endif

                                @if (isset($analysis['ideal_candidate_profile']['tailoring_recommendations']['cover_letter']))
                                    <div>
                                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 mb-2">
                                            <flux:icon.document-text class="inline size-4" />
                                            Cover Letter:
                                        </p>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400 ml-6">
                                            {{ $analysis['ideal_candidate_profile']['tailoring_recommendations']['cover_letter'] }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <p class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">
                                    Actionable Takeaway
                                </p>
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    Position yourself as a well-rounded candidate who is not only technically skilled but also aligned with the role's requirements and eager to contribute to the organization's mission.
                                </p>
                            </div>
                        @endif
                    </div>
                </flux:card>
            @endif
        </div>
    @endif
</div>

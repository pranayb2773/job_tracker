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
        <div class="flex items-center gap-3">
            <flux:badge color="blue" size="lg">
                <flux:icon.sparkles class="size-4" />
                {{ $remainingAnalyses }} remaining today
            </flux:badge>
        </div>
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
            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3">
                <flux:button
                    wire:click="clearAnalysis"
                    variant="ghost"
                    icon="arrow-path"
                >
                    Analyze Another Role
                </flux:button>
            </div>

            {{-- Score Summary Card --}}
            @if (isset($analysis['application_strategy']['recommendation_score']))
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Score Gauge --}}
                    <flux:card class="col-span-1">
                        <div class="flex flex-col items-center justify-center py-8">
                            <div class="relative size-48">
                                <svg
                                    class="size-48 transform -rotate-90"
                                    viewBox="0 0 200 200"
                                >
                                    {{-- Background circle --}}
                                    <circle
                                        cx="100"
                                        cy="100"
                                        r="85"
                                        stroke="currentColor"
                                        stroke-width="20"
                                        fill="none"
                                        class="text-zinc-200 dark:text-zinc-700"
                                    />
                                    {{-- Progress circle --}}
                                    <circle
                                        cx="100"
                                        cy="100"
                                        r="85"
                                        stroke="currentColor"
                                        stroke-width="20"
                                        fill="none"
                                        stroke-linecap="round"
                                        class="{{ $analysis['application_strategy']['recommendation_score'] >= 80 ? 'text-green-500' : ($analysis['application_strategy']['recommendation_score'] >= 60 ? 'text-blue-500' : ($analysis['application_strategy']['recommendation_score'] >= 40 ? 'text-yellow-500' : 'text-red-500')) }}"
                                        style="
                                            stroke-dasharray: {{ 2 * 3.14159 * 85 }};
                                            stroke-dashoffset: {{ 2 * 3.14159 * 85 * (1 - $analysis['application_strategy']['recommendation_score'] / 100) }};
                                        "
                                    />
                                </svg>
                                <div
                                    class="absolute inset-0 flex flex-col items-center justify-center"
                                >
                                    <div
                                        class="text-5xl font-bold text-zinc-900 dark:text-white"
                                    >
                                        {{ $analysis['application_strategy']['recommendation_score'] }}
                                    </div>
                                    <div
                                        class="text-sm font-medium text-zinc-600 dark:text-zinc-400"
                                    >
                                        {{ $analysis['application_strategy']['recommendation_label'] ?? 'SCORE' }}
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 text-center">
                                <p
                                    class="text-sm text-zinc-600 dark:text-zinc-400 max-w-xs"
                                >
                                    Overall recommendation score based on role clarity, requirements, and opportunity quality
                                </p>
                            </div>
                        </div>
                    </flux:card>

                    {{-- Overview Summary --}}
                    <flux:card class="col-span-1 lg:col-span-2">
                        <div class="space-y-6">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <flux:heading size="lg">Role Overview</flux:heading>
                                    <flux:badge color="blue" variant="outline">
                                        <flux:icon.sparkles class="size-3.5" />
                                        AI Analysis
                                    </flux:badge>
                                </div>
                                <p
                                    class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed"
                                >
                                    {{ $analysis['comprehensive_overview']['summary'] ?? 'No summary available.' }}
                                </p>
                            </div>

                            @if (isset($analysis['comprehensive_overview']['actionable_takeaway']))
                                <flux:separator variant="subtle" />

                                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                    <div class="flex items-start gap-3">
                                        <flux:icon.light-bulb class="size-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                                        <div>
                                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">
                                                Key Takeaway
                                            </p>
                                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                                {{ $analysis['comprehensive_overview']['actionable_takeaway'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </flux:card>
                </div>
            @endif

            {{-- Keywords Section --}}
            @if (isset($analysis['keywords']) && count($analysis['keywords']) > 0)
                <flux:card>
                    <flux:heading size="lg" class="mb-1">Keywords to Emphasize</flux:heading>
                    <flux:subheading class="mb-6">
                        Most relevant terms and phrases for your CV and cover letter
                    </flux:subheading>

                    <flux:separator variant="subtle" class="mb-6" />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($analysis['keywords'] as $item)
                            <div class="flex gap-3 p-4 rounded-lg bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800">
                                <div class="flex-shrink-0">
                                    <flux:badge
                                        :color="match($item['priority'] ?? 'medium') {
                                            'high' => 'red',
                                            'medium' => 'yellow',
                                            'low' => 'zinc',
                                            default => 'zinc'
                                        }"
                                        size="sm"
                                    >
                                        {{ ucfirst($item['priority'] ?? 'medium') }}
                                    </flux:badge>
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
                </flux:card>
            @endif

            {{-- Skills Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Hard Skills --}}
                @if (isset($analysis['hard_skills']) && count($analysis['hard_skills']) > 0)
                    <flux:card>
                        <flux:heading size="lg" class="mb-1">Hard Skills</flux:heading>
                        <flux:subheading class="mb-6">
                            Critical technical competencies
                        </flux:subheading>

                        <flux:separator variant="subtle" class="mb-6" />

                        <div class="space-y-5">
                            @foreach ($analysis['hard_skills'] as $skill)
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <flux:heading size="sm" class="text-zinc-900 dark:text-zinc-100">
                                            {{ $skill['skill'] }}
                                        </flux:heading>
                                        @if ($skill['required'] ?? false)
                                            <flux:badge color="red" size="sm">Required</flux:badge>
                                        @endif
                                    </div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $skill['description'] }}
                                    </p>
                                    @if (isset($skill['example']))
                                        <div class="mt-2 p-3 bg-zinc-100 dark:bg-zinc-800 rounded border border-zinc-200 dark:border-zinc-700">
                                            <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
                                                <flux:icon.light-bulb class="inline size-3.5" />
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
                    </flux:card>
                @endif

                {{-- Soft Skills --}}
                @if (isset($analysis['soft_skills']) && count($analysis['soft_skills']) > 0)
                    <flux:card>
                        <flux:heading size="lg" class="mb-1">Soft Skills</flux:heading>
                        <flux:subheading class="mb-6">
                            Essential interpersonal qualities
                        </flux:subheading>

                        <flux:separator variant="subtle" class="mb-6" />

                        <div class="space-y-5">
                            @foreach ($analysis['soft_skills'] as $skill)
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <flux:heading size="sm" class="text-zinc-900 dark:text-zinc-100">
                                            {{ $skill['skill'] }}
                                        </flux:heading>
                                        @if (isset($skill['importance']))
                                            <flux:badge
                                                :color="match($skill['importance']) {
                                                    'critical' => 'red',
                                                    'high' => 'yellow',
                                                    'medium' => 'zinc',
                                                    default => 'zinc'
                                                }"
                                                size="sm"
                                            >
                                                {{ ucfirst($skill['importance']) }}
                                            </flux:badge>
                                        @endif
                                    </div>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $skill['description'] }}
                                    </p>
                                    @if (isset($skill['example']))
                                        <div class="mt-2 p-3 bg-zinc-100 dark:bg-zinc-800 rounded border border-zinc-200 dark:border-zinc-700">
                                            <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">
                                                <flux:icon.light-bulb class="inline size-3.5" />
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
                    </flux:card>
                @endif
            </div>

            {{-- Ideal Candidate Profile --}}
            @if (isset($analysis['ideal_candidate_profile']))
                <flux:card>
                    <flux:heading size="lg" class="mb-1">Ideal Candidate Profile</flux:heading>
                    <flux:subheading class="mb-6">
                        Target attributes and application recommendations
                    </flux:subheading>

                    <flux:separator variant="subtle" class="mb-6" />

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @if (isset($analysis['ideal_candidate_profile']['experience_level']))
                                <div class="p-4 rounded-lg bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800">
                                    <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-2">
                                        Experience Level
                                    </p>
                                    <flux:badge color="blue" size="lg">
                                        {{ $analysis['ideal_candidate_profile']['experience_level'] }}
                                    </flux:badge>
                                </div>
                            @endif
                            @if (isset($analysis['ideal_candidate_profile']['years_of_experience']))
                                <div class="p-4 rounded-lg bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800">
                                    <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-2">
                                        Years of Experience
                                    </p>
                                    <p class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                                        {{ $analysis['ideal_candidate_profile']['years_of_experience'] }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        @if (isset($analysis['ideal_candidate_profile']['key_attributes']) && count($analysis['ideal_candidate_profile']['key_attributes']) > 0)
                            <div>
                                <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-3">
                                    Key Attributes
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($analysis['ideal_candidate_profile']['key_attributes'] as $attribute)
                                        <flux:badge color="purple" variant="outline">
                                            {{ $attribute }}
                                        </flux:badge>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (isset($analysis['ideal_candidate_profile']['tailoring_recommendations']) && count($analysis['ideal_candidate_profile']['tailoring_recommendations']) > 0)
                            <div>
                                <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-3">
                                    <flux:icon.check-circle class="inline size-4" />
                                    Tailoring Recommendations
                                </p>
                                <ul class="space-y-2">
                                    @foreach ($analysis['ideal_candidate_profile']['tailoring_recommendations'] as $index => $recommendation)
                                        <li class="flex gap-3 text-sm text-zinc-600 dark:text-zinc-400">
                                            <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $index + 1 }}.</span>
                                            <span>{{ $recommendation }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </flux:card>
            @endif

            {{-- Red & Green Flags --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Red Flags --}}
                @if (isset($analysis['red_flags']) && count($analysis['red_flags']) > 0)
                    <flux:card>
                        <flux:heading size="lg" class="text-red-600 dark:text-red-400 mb-6">
                            <flux:icon.exclamation-triangle class="inline size-5" />
                            Red Flags
                        </flux:heading>

                        <div class="space-y-3">
                            @foreach ($analysis['red_flags'] as $flag)
                                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                                    <div class="flex items-start gap-3">
                                        <flux:badge
                                            :color="match($flag['severity'] ?? 'medium') {
                                                'high' => 'red',
                                                'medium' => 'yellow',
                                                'low' => 'zinc',
                                                default => 'zinc'
                                            }"
                                            size="sm"
                                            class="flex-shrink-0"
                                        >
                                            {{ ucfirst($flag['severity'] ?? 'medium') }}
                                        </flux:badge>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm text-red-900 dark:text-red-100 mb-1">
                                                {{ $flag['flag'] }}
                                            </p>
                                            <p class="text-xs text-red-700 dark:text-red-300">
                                                {{ $flag['explanation'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </flux:card>
                @endif

                {{-- Green Flags --}}
                @if (isset($analysis['green_flags']) && count($analysis['green_flags']) > 0)
                    <flux:card>
                        <flux:heading size="lg" class="text-green-600 dark:text-green-400 mb-6">
                            <flux:icon.check-circle class="inline size-5" />
                            Green Flags
                        </flux:heading>

                        <div class="space-y-3">
                            @foreach ($analysis['green_flags'] as $flag)
                                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                    <div class="flex items-start gap-3">
                                        <flux:badge
                                            :color="match($flag['significance'] ?? 'medium') {
                                                'high' => 'green',
                                                'medium' => 'blue',
                                                'low' => 'zinc',
                                                default => 'zinc'
                                            }"
                                            size="sm"
                                            class="flex-shrink-0"
                                        >
                                            {{ ucfirst($flag['significance'] ?? 'medium') }}
                                        </flux:badge>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm text-green-900 dark:text-green-100 mb-1">
                                                {{ $flag['flag'] }}
                                            </p>
                                            <p class="text-xs text-green-700 dark:text-green-300">
                                                {{ $flag['explanation'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </flux:card>
                @endif
            </div>

            {{-- Application Strategy --}}
            @if (isset($analysis['application_strategy']))
                <flux:card>
                    <flux:heading size="lg" class="mb-1">Application Strategy</flux:heading>
                    <flux:subheading class="mb-6">
                        Actionable recommendations to maximize your chances
                    </flux:subheading>

                    <flux:separator variant="subtle" class="mb-6" />

                    <div class="space-y-6">
                        @if (isset($analysis['application_strategy']['key_selling_points']) && count($analysis['application_strategy']['key_selling_points']) > 0)
                            <div>
                                <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-3">
                                    <flux:icon.star class="inline size-4 text-yellow-500" />
                                    Key Selling Points
                                </p>
                                <ul class="space-y-2">
                                    @foreach ($analysis['application_strategy']['key_selling_points'] as $index => $point)
                                        <li class="flex gap-3 text-sm text-zinc-600 dark:text-zinc-400">
                                            <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $index + 1 }}.</span>
                                            <span>{{ $point }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (isset($analysis['application_strategy']['cover_letter_focus']) && count($analysis['application_strategy']['cover_letter_focus']) > 0)
                            <div>
                                <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-3">
                                    <flux:icon.document-text class="inline size-4" />
                                    Cover Letter Focus
                                </p>
                                <ul class="space-y-2">
                                    @foreach ($analysis['application_strategy']['cover_letter_focus'] as $index => $focus)
                                        <li class="flex gap-3 text-sm text-zinc-600 dark:text-zinc-400">
                                            <span class="font-semibold text-purple-600 dark:text-purple-400">{{ $index + 1 }}.</span>
                                            <span>{{ $focus }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (isset($analysis['application_strategy']['interview_preparation']) && count($analysis['application_strategy']['interview_preparation']) > 0)
                            <div>
                                <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-3">
                                    <flux:icon.chat-bubble-left-right class="inline size-4" />
                                    Interview Preparation
                                </p>
                                <ul class="space-y-2">
                                    @foreach ($analysis['application_strategy']['interview_preparation'] as $index => $prep)
                                        <li class="flex gap-3 text-sm text-zinc-600 dark:text-zinc-400">
                                            <span class="font-semibold text-green-600 dark:text-green-400">{{ $index + 1 }}.</span>
                                            <span>{{ $prep }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </flux:card>
            @endif
        </div>
    @endif
</div>

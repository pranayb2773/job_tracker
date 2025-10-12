<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    {{-- Breadcrumbs --}}
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('dashboard') }}">
            {{ __('Home') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('documents.list') }}">
            {{ __('Documents') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $document->title }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ $document->title }}</flux:heading>
            <flux:subheading class="mt-1">
                AI-Powered CV Analysis
                @if ($document->analysis)
                        - {{ $document->analyzed_at->diffForHumans() }}
                @endif
            </flux:subheading>
        </div>
        <flux:button
            wire:click="analyzeCV"
            variant="primary"
            icon:trailing="sparkles"
            :disabled="$isAnalyzing"
        >
            @if ($isAnalyzing)
                <flux:icon.arrow-path class="animate-spin" />
                Analyzing...
            @elseif ($analysis)
                Regenerate Analysis
            @else
                    Generate Analysis
            @endif
        </flux:button>
    </div>

    @if ($analysis)
        {{-- Overall Score Card --}}
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
                                class="{{ $analysis['overall_score'] >= 80 ? 'text-green-500' : ($analysis['overall_score'] >= 60 ? 'text-yellow-500' : 'text-red-500') }}"
                                style="
                                    stroke-dasharray: {{ 2 * 3.14159 * 85 }};
                                    stroke-dashoffset: {{ 2 * 3.14159 * 85 * (1 - $analysis['overall_score'] / 100) }};
                                "
                            />
                        </svg>
                        <div
                            class="absolute inset-0 flex flex-col items-center justify-center"
                        >
                            <div
                                class="text-5xl font-bold text-zinc-900 dark:text-white"
                            >
                                {{ $analysis['overall_score'] }}
                            </div>
                            <div
                                class="text-sm text-zinc-600 dark:text-zinc-400"
                            >
                                {{ $analysis['score_label'] }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 text-center">
                        <p
                            class="text-sm text-zinc-600 dark:text-zinc-400 max-w-xs"
                        >
                            {{ $analysis['score_description'] }}
                        </p>
                    </div>
                </div>
            </flux:card>

            {{-- Summary and Recommendations --}}
            <flux:card class="col-span-1 lg:col-span-2">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Summary</flux:heading>
                        <p
                            class="mt-2 text-sm text-zinc-700 dark:text-zinc-300"
                        >
                            {{ $analysis['summary'] }}
                        </p>
                    </div>

                    <flux:separator variant="subtle" />

                    <div>
                        <flux:heading size="lg">
                            Top Recommendations
                        </flux:heading>
                        <ul
                            class="mt-3 space-y-2 text-sm text-zinc-700 dark:text-zinc-300"
                        >
                            @foreach ($analysis['top_recommendations'] as $index => $recommendation)
                                <li class="flex gap-2">
                                    <span class="font-semibold">
                                        {{ $index + 1 }}.
                                    </span>
                                    <span>{{ $recommendation }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </flux:card>
        </div>

        {{-- Scoring Dimensions --}}
        <flux:card>
            <flux:heading size="lg" class="mb-6">
                Scoring Dimensions
            </flux:heading>
            <div class="space-y-4">
                @foreach ($analysis['scoring_dimensions'] as $key => $dimension)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span
                                class="text-sm font-medium text-zinc-900 dark:text-white"
                            >
                                {{ $dimension['label'] }}
                            </span>
                            <flux:badge
                                size="sm"
                                :color="$dimension['score'] >= 80 ? 'green' : ($dimension['score'] >= 60 ? 'blue' : 'red')"
                            >
                                {{ $dimension['score'] }}
                            </flux:badge>
                        </div>
                        <div
                            class="w-full bg-zinc-200 rounded-full h-2.5 dark:bg-zinc-700"
                        >
                            <div
                                class="h-2.5 rounded-full {{ $dimension['score'] >= 80 ? 'bg-green-500' : ($dimension['score'] >= 60 ? 'bg-blue-500' : 'bg-red-500') }}"
                                style="width: {{ $dimension['score'] }}%"
                            ></div>
                        </div>
                        @if (isset($dimension['description']))
                            <p
                                class="mt-1 text-xs text-zinc-600 dark:text-zinc-400"
                            >
                                {{ $dimension['description'] }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        </flux:card>

        {{-- Penalties --}}
        @if (isset($analysis['penalties']) && count($analysis['penalties']) > 0)
            <flux:card>
                <flux:heading size="lg" class="mb-4">Penalties</flux:heading>
                <ul class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
                    @foreach ($analysis['penalties'] as $penalty)
                        <li class="flex items-start gap-2">
                            <flux:icon.exclamation-triangle
                                class="size-5 text-amber-500 shrink-0 mt-0.5"
                            />
                            <span>{{ $penalty }}</span>
                        </li>
                    @endforeach
                </ul>
            </flux:card>
        @endif

        {{-- Section Analysis --}}
        <flux:card>
            <flux:heading size="lg" class="mb-6">Section Analysis</flux:heading>
            <div class="space-y-6">
                @foreach ($analysis['section_analysis'] as $sectionKey => $section)
                    <div
                        class="flex items-start gap-4 pb-6 border-b border-zinc-200 dark:border-zinc-700 last:border-0"
                    >
                        <div class="shrink-0 mt-1">
                            @if ($section['status'] === 'success')
                                <flux:icon.check-circle
                                    variant="solid"
                                    class="size-6 text-green-500"
                                />
                            @elseif ($section['status'] === 'warning')
                                <flux:icon.exclamation-triangle
                                    variant="solid"
                                    class="size-6 text-amber-500"
                                />
                            @else
                                <flux:icon.x-circle
                                    variant="solid"
                                    class="size-6 text-red-500"
                                />
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3
                                class="font-semibold text-zinc-900 dark:text-white capitalize"
                            >
                                {{ str_replace('_', ' ', $sectionKey) }}
                            </h3>
                            <p
                                class="mt-1 text-sm text-zinc-700 dark:text-zinc-300"
                            >
                                {{ $section['feedback'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </flux:card>
    @else
        {{-- Empty State --}}
        <flux:card class="py-16">
            <div
                class="flex flex-col items-center justify-center space-y-4 text-center"
            >
                <div
                    class="flex size-16 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/20"
                >
                    <flux:icon.sparkles
                        variant="solid"
                        class="size-8 text-purple-600 dark:text-purple-500"
                    />
                </div>
                <div>
                    <flux:heading size="lg">
                        Generate AI-Powered Analysis
                    </flux:heading>
                    <flux:subheading class="mt-2 max-w-md">
                        Click the "Generate Analysis" button above to analyze
                        this CV with AI and get comprehensive ATS scoring and
                        recommendations.
                    </flux:subheading>
                </div>
            </div>
        </flux:card>
    @endif
</div>

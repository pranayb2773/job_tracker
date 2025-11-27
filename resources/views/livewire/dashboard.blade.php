<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <!-- Breadcrumbs -->
    <flux:breadcrumbs>
        <flux:breadcrumbs.item>{{ __('Dashboard') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <!-- Title -->
    <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>

    <!-- Widgets -->
    <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <!-- Applications by Status Widget -->
        <flux:card class="space-y-4">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">
                    {{ __('Applications by Status') }}
                </flux:heading>
                <flux:icon.rectangle-stack variant="outline" class="size-6" />
            </div>

            @php
                $statusColors = [
                    'applied' => '#8b5cf6', // violet
                    'screening' => '#3b82f6', // blue
                    'interview' => '#f59e0b', // amber
                    'technical_test' => '#eab308', // yellow
                    'final_interview' => '#ec4899', // pink
                    'offer' => '#22c55e', // green
                    'accepted' => '#10b981', // emerald
                    'rejected' => '#ef4444', // red
                    'withdrawn' => '#f43f5e', // rose
                ];
                $statusTotal = collect($applicationsByStatus)->sum();
                $r = 16;
                $circ = 2 * M_PI * $r;
                $segments = collect($applicationsByStatus)
                    ->filter(fn ($c) => $c > 0)
                    ->map(function ($c, $key) use ($statusColors) {
                        $label = \App\Enums\ApplicationStatus::from($key)->getLabel();
                        $color = $statusColors[$key] ?? '#71717a';
                        return ['key' => $key, 'count' => $c, 'label' => $label, 'color' => $color];
                    })
                    ->values();
            @endphp

            @if ($statusTotal === 0)
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('No data') }}
                </p>
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="flex items-center justify-center">
                        <svg
                            viewBox="0 0 40 40"
                            width="160"
                            height="160"
                            class="-rotate-90"
                        >
                            <circle
                                cx="20"
                                cy="20"
                                r="16"
                                fill="none"
                                stroke="#e5e7eb"
                                stroke-width="8"
                            />
                            @php
                                $accum = 0;
                            @endphp

                            @foreach ($segments as $seg)
                                @php
                                    $portion = $seg['count'] / max($statusTotal, 1);
                                    $dash = $portion * $circ;
                                    $gap = $circ - $dash;
                                @endphp

                                <circle
                                    cx="20"
                                    cy="20"
                                    r="16"
                                    fill="none"
                                    stroke="{{ $seg['color'] }}"
                                    stroke-width="8"
                                    stroke-dasharray="{{ $dash }} {{ $gap }}"
                                    stroke-dashoffset="{{ -$accum }}"
                                />
                                @php
                                    $accum += $dash;
                                @endphp
                            @endforeach
                        </svg>
                    </div>
                    <div class="space-y-2">
                        @foreach ($segments as $seg)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-block h-3 w-3 rounded"
                                        style="background: {{ $seg['color'] }}"
                                    ></span>
                                    <span
                                        class="text-sm text-zinc-800 dark:text-zinc-200"
                                    >
                                        {{ $seg['label'] }}
                                    </span>
                                </div>
                                <span
                                    class="text-sm font-semibold text-zinc-700 dark:text-zinc-300"
                                >
                                    {{ $seg['count'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </flux:card>

        <!-- Documents by Type Widget -->
        <flux:card class="space-y-4">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">
                    {{ __('Documents by Type') }}
                </flux:heading>
                <flux:icon.document-text variant="outline" class="size-6" />
            </div>

            @php
                $docColors = [
                    'curriculum_vitae' => '#f97316', // orange
                    'cover_letter' => '#22c55e', // green
                    'letter_of_interest' => '#ec4899', // pink
                    'follow_up_letter' => '#a855f7', // purple
                    'acceptance_letter' => '#84cc16', // lime
                    'decline_letter' => '#ef4444', // red
                    'other' => '#71717a', // zinc
                ];
                $docTotal = collect($documentsByType)->sum();
                $r2 = 16;
                $circ2 = 2 * M_PI * $r2;
                $segments2 = collect($documentsByType)
                    ->filter(fn ($c) => $c > 0)
                    ->map(function ($c, $key) use ($docColors) {
                        $label = \App\Enums\DocumentType::from($key)->getLabel();
                        $color = $docColors[$key] ?? '#71717a';
                        return ['key' => $key, 'count' => $c, 'label' => $label, 'color' => $color];
                    })
                    ->values();
            @endphp

            @if ($docTotal === 0)
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('No data') }}
                </p>
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="flex items-center justify-center">
                        <svg
                            viewBox="0 0 40 40"
                            width="160"
                            height="160"
                            class="-rotate-90"
                        >
                            <circle
                                cx="20"
                                cy="20"
                                r="16"
                                fill="none"
                                stroke="#e5e7eb"
                                stroke-width="8"
                            />
                            @php
                                $accum2 = 0;
                            @endphp

                            @foreach ($segments2 as $seg)
                                @php
                                    $portion = $seg['count'] / max($docTotal, 1);
                                    $dash = $portion * $circ2;
                                    $gap = $circ2 - $dash;
                                @endphp

                                <circle
                                    cx="20"
                                    cy="20"
                                    r="16"
                                    fill="none"
                                    stroke="{{ $seg['color'] }}"
                                    stroke-width="8"
                                    stroke-dasharray="{{ $dash }} {{ $gap }}"
                                    stroke-dashoffset="{{ -$accum2 }}"
                                />
                                @php
                                    $accum2 += $dash;
                                @endphp
                            @endforeach
                        </svg>
                    </div>
                    <div class="space-y-2">
                        @foreach ($segments2 as $seg)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-block h-3 w-3 rounded"
                                        style="background: {{ $seg['color'] }}"
                                    ></span>
                                    <span
                                        class="text-sm text-zinc-800 dark:text-zinc-200"
                                    >
                                        {{ $seg['label'] }}
                                    </span>
                                </div>
                                <span
                                    class="text-sm font-semibold text-zinc-700 dark:text-zinc-300"
                                >
                                    {{ $seg['count'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </flux:card>

        <!-- AI Analysis Limit Widget -->
        <flux:card class="space-y-4">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">
                    {{ __('AI Analysis Limit') }}
                </flux:heading>
                <flux:icon.sparkles variant="outline" class="size-6" />
            </div>

            @php
                $analysisTotal = (int) config('ai.ai_analysis.rate_limit.daily_limit', 30);
                $analysisRemaining = (int) $remainingAnalyses;
                $analysisUsed = max(0, $analysisTotal - $analysisRemaining);
                $r3 = 22;
                $circ3 = 2 * M_PI * $r3;
                $portionRemain = $analysisRemaining / max($analysisTotal, 1);
                $dashRemain = $portionRemain * $circ3;
                $gapRemain = $circ3 - $dashRemain;
            @endphp

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 items-center">
                <div class="relative mx-auto">
                    <svg
                        viewBox="0 0 60 60"
                        width="160"
                        height="160"
                        class="-rotate-90"
                    >
                        <circle
                            cx="30"
                            cy="30"
                            r="22"
                            fill="none"
                            stroke="#e5e7eb"
                            stroke-width="10"
                        />
                        <circle
                            cx="30"
                            cy="30"
                            r="22"
                            fill="none"
                            stroke="#22c55e"
                            stroke-width="10"
                            stroke-dasharray="{{ $dashRemain }} {{ $gapRemain }}"
                            stroke-linecap="round"
                        />
                    </svg>
                    <div class="absolute inset-0 grid place-items-center">
                        <div class="text-center">
                            <div
                                class="text-3xl font-bold text-zinc-900 dark:text-zinc-100"
                            >
                                {{ $analysisRemaining }}
                            </div>
                            <div
                                class="text-xs text-zinc-600 dark:text-zinc-400"
                            >
                                {{ __('remaining') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-block h-3 w-3 rounded bg-emerald-500"
                            ></span>
                            <span
                                class="text-sm text-zinc-800 dark:text-zinc-200"
                            >
                                {{ __('Remaining') }}
                            </span>
                        </div>
                        <span
                            class="text-sm font-semibold text-zinc-700 dark:text-zinc-300"
                        >
                            {{ $analysisRemaining }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-block h-3 w-3 rounded bg-zinc-300 dark:bg-zinc-600"
                            ></span>
                            <span
                                class="text-sm text-zinc-800 dark:text-zinc-200"
                            >
                                {{ __('Used') }}
                            </span>
                        </div>
                        <span
                            class="text-sm font-semibold text-zinc-700 dark:text-zinc-300"
                        >
                            {{ $analysisUsed }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ __('Daily limit') }}
                        </div>
                        <span
                            class="text-sm font-semibold text-zinc-700 dark:text-zinc-300"
                        >
                            {{ $analysisTotal }}
                        </span>
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ __('Resets at midnight') }}
                    </div>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Latest Applications Table -->
    <flux:card>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <flux:heading size="lg">
                    {{ __('Latest Applications') }}
                </flux:heading>
                <flux:button
                    href="{{ route('applications.list') }}"
                    wire:navigate
                    variant="ghost"
                    size="sm"
                    icon-trailing="arrow-right"
                >
                    {{ __('View All') }}
                </flux:button>
            </div>

            @if ($latestApplications->isEmpty())
                <div
                    class="flex flex-col items-center justify-center py-12 text-center"
                >
                    <flux:icon.rectangle-stack
                        variant="outline"
                        class="mb-4 size-12 text-zinc-400 dark:text-zinc-600"
                    />
                    <flux:heading size="lg" class="mb-2">
                        {{ __('No applications yet') }}
                    </flux:heading>
                    <flux:subheading class="mb-4">
                        {{ __('Get started by creating your first job application') }}
                    </flux:subheading>
                    <flux:button
                        href="{{ route('applications.create') }}"
                        wire:navigate
                        variant="primary"
                        icon-trailing="plus"
                    >
                        {{ __('Create Application') }}
                    </flux:button>
                </div>
            @else
                <div
                    class="relative overflow-x-auto rounded-lg border border-zinc-800/10 dark:border-white/20"
                >
                    <table
                        class="[:where(&)]:min-w-full table-fixed text-zinc-800 divide-y divide-zinc-800/10 dark:divide-white/20 whitespace-nowrap [&_dialog]:whitespace-normal [&_[popover]]:whitespace-normal [&_tr:hover]:bg-zinc-50 dark:[&_tr:hover]:bg-zinc-800/50"
                    >
                        <flux:table.columns
                            class="bg-zinc-50 dark:bg-zinc-600/40"
                        >
                            <flux:table.column class="!pl-4">
                                {{ __('Job Title') }}
                            </flux:table.column>
                            <flux:table.column>
                                {{ __('Organisation') }}
                            </flux:table.column>
                            <flux:table.column>
                                {{ __('Status') }}
                            </flux:table.column>
                            <flux:table.column>
                                {{ __('Priority') }}
                            </flux:table.column>
                            <flux:table.column>
                                {{ __('Last Updated') }}
                            </flux:table.column>
                            <flux:table.column></flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach ($latestApplications as $application)
                                <flux:table.row>
                                    <flux:table.cell
                                        class="min-w-6 w-1/4 !pl-4 font-medium text-zinc-900 dark:text-zinc-100"
                                    >
                                        {{ $application->job_title }}
                                    </flux:table.cell>

                                    <flux:table.cell class="min-w-6 w-1/4">
                                        {{ $application->organisation }}
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <flux:badge
                                            size="sm"
                                            :color="$application->status->getColor()"
                                            variant="pill"
                                        >
                                            {{ $application->status->getLabel() }}
                                        </flux:badge>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        @if ($application->priority)
                                            <flux:badge
                                                size="sm"
                                                :color="$application->priority->getColor()"
                                                variant="pill"
                                            >
                                                {{ $application->priority->getLabel() }}
                                            </flux:badge>
                                        @else
                                            <span
                                                class="text-sm text-zinc-500 dark:text-zinc-400"
                                            >
                                                -
                                            </span>
                                        @endif
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <span
                                            class="text-sm text-zinc-600 dark:text-zinc-400"
                                        >
                                            {{ $application->updated_at->diffForHumans() }}
                                        </span>
                                    </flux:table.cell>

                                    <flux:table.cell>
                                        <flux:button
                                            href="{{ route('applications.show', $application) }}"
                                            wire:navigate
                                            variant="ghost"
                                            size="sm"
                                            icon="eye"
                                        >
                                            {{ __('View') }}
                                        </flux:button>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </table>
                </div>
            @endif
        </div>
    </flux:card>
</div>

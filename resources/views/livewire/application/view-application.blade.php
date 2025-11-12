<div class="mx-auto flex h-full w-full flex-1 flex-col gap-4">
    {{-- Breadcrumbs --}}
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('dashboard') }}" wire:navigate>
            {{ __('Home') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item
            href="{{ route('applications.list') }}"
            wire:navigate
        >
            {{ __('Applications') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>
            {{ $application->job_title }}
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div>
            <flux:heading size="xl">
                {{ $application->job_title }}
            </flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ $application->organisation }}
            </flux:text>
        </div>
        <div class="flex items-start gap-3">
            <flux:button
                href="{{ route('applications.list') }}"
                variant="ghost"
                size="sm"
            >
                {{ __('Back') }}
            </flux:button>
            <flux:button
                href="{{ route('applications.edit', $application) }}"
                variant="outline"
                size="sm"
                icon="pencil"
            >
                {{ __('Edit') }}
            </flux:button>
            <flux:button variant="danger" size="sm" icon="trash">
                {{ __('Delete') }}
            </flux:button>
        </div>
    </div>

    {{-- Tabs --}}
    <flux:tab.group>
        <flux:tabs>
            <flux:tab name="details">{{ __('Details') }}</flux:tab>
            @if ($application->job_description)
                <flux:tab name="job-description">
                    {{ __('Job Description') }}
                </flux:tab>
            @endif

            @if ($application->notes)
                <flux:tab name="notes">{{ __('Notes') }}</flux:tab>
            @endif

            <flux:tab name="ai">
                <div class="flex items-center gap-2">
                    {{ __('AI') }}
                    <flux:icon.sparkles class="size-4" />
                </div>
            </flux:tab>
        </flux:tabs>

        {{-- Details Tab Panel --}}
        <flux:tab.panel name="details">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                {{-- Left Column (2/3 width) --}}
                <div class="space-y-6 lg:col-span-2">
                    {{-- Basic Information --}}
                    <div
                        class="space-y-6 rounded-lg bg-zinc-100 p-6 text-sm dark:bg-zinc-900"
                    >
                        <flux:heading size="lg">
                            {{ __('Basic Information') }}
                        </flux:heading>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <flux:label>{{ __('Job Title') }}</flux:label>
                                <p
                                    class="mt-2 text-zinc-900 dark:text-zinc-100"
                                >
                                    {{ $application->job_title }}
                                </p>
                            </div>

                            <div>
                                <flux:label>
                                    {{ __('Organisation') }}
                                </flux:label>
                                <p
                                    class="mt-2 text-zinc-900 dark:text-zinc-100"
                                >
                                    {{ $application->organisation }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Job Details --}}
                    <div
                        class="space-y-6 rounded-lg bg-zinc-100 p-6 text-sm dark:bg-zinc-900"
                    >
                        <flux:heading size="lg">
                            {{ __('Job Details') }}
                        </flux:heading>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            @if ($application->location)
                                <div>
                                    <flux:label>
                                        {{ __('Location') }}
                                    </flux:label>
                                    <p
                                        class="mt-2 text-zinc-900 dark:text-zinc-100"
                                    >
                                        {{ $application->location }}
                                    </p>
                                </div>
                            @endif

                            @if ($application->type)
                                <div>
                                    <flux:label>
                                        {{ __('Job Type') }}
                                    </flux:label>
                                    <p
                                        class="mt-2 text-zinc-900 dark:text-zinc-100"
                                    >
                                        {{ $application->type->getLabel() }}
                                    </p>
                                </div>
                            @endif

                            @if ($application->work_arrangement)
                                <div>
                                    <flux:label>
                                        {{ __('Working Model') }}
                                    </flux:label>
                                    <p
                                        class="mt-2 text-zinc-900 dark:text-zinc-100"
                                    >
                                        {{ ucfirst($application->work_arrangement) }}
                                    </p>
                                </div>
                            @endif

                            @if ($application->deadline)
                                <div>
                                    <flux:label>
                                        {{ __('Deadline') }}
                                    </flux:label>
                                    <p
                                        class="mt-2 text-zinc-900 dark:text-zinc-100"
                                    >
                                        {{ $application->deadline->format('d M Y') }}
                                    </p>
                                </div>
                            @endif

                            @if ($application->salary_range)
                                <div>
                                    <flux:label>
                                        {{ __('Salary Range') }}
                                    </flux:label>
                                    <p
                                        class="mt-2 text-zinc-900 dark:text-zinc-100"
                                    >
                                        {{ $application->salary_range }}
                                    </p>
                                </div>
                            @endif

                            @if ($application->salary_min)
                                <div>
                                    <flux:label>
                                        {{ __('Minimum Salary') }}
                                    </flux:label>
                                    <p
                                        class="mt-2 text-zinc-900 dark:text-zinc-100"
                                    >
                                        {{ number_format($application->salary_min) }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Source --}}
                    <div
                        class="space-y-6 rounded-lg bg-zinc-100 p-6 text-sm dark:bg-zinc-900"
                    >
                        <flux:heading size="lg">
                            {{ __('Source') }}
                        </flux:heading>

                        <div class="space-y-6">
                            <div>
                                <flux:label>
                                    {{ __('Link to job advert') }}
                                </flux:label>
                                @if ($application->job_url)
                                    <div class="mt-2 flex items-center gap-2">
                                        <a
                                            href="{{ $application->job_url }}"
                                            target="_blank"
                                            class="break-all text-blue-600 hover:underline dark:text-blue-400"
                                        >
                                            {{ $application->job_url }}
                                        </a>
                                        <a
                                            href="{{ $application->job_url }}"
                                            target="_blank"
                                            class="shrink-0"
                                        >
                                            <flux:icon.arrow-top-right-on-square
                                                class="size-5 text-blue-600 dark:text-blue-400"
                                            />
                                        </a>
                                    </div>
                                @else
                                    <p
                                        class="mt-2 text-zinc-500 dark:text-zinc-400"
                                    >
                                        {{ __('Not provided') }}
                                    </p>
                                @endif
                            </div>

                            <div>
                                <flux:label>
                                    {{ __('Source type') }}
                                </flux:label>
                                <p
                                    class="mt-2 text-zinc-900 dark:text-zinc-100"
                                >
                                    {{ $application->source ?? __('Not specified') }}
                                </p>
                            </div>

                            <div>
                                <flux:label>
                                    {{ __('Link to advert in job search website') }}
                                </flux:label>
                                @if ($application->source_url)
                                    <div class="mt-2 flex items-center gap-2">
                                        <a
                                            href="{{ $application->source_url }}"
                                            target="_blank"
                                            class="break-all text-blue-600 hover:underline dark:text-blue-400"
                                        >
                                            {{ $application->source_url }}
                                        </a>
                                        <a
                                            href="{{ $application->source_url }}"
                                            target="_blank"
                                            class="shrink-0"
                                        >
                                            <flux:icon.arrow-top-right-on-square
                                                class="size-5 text-blue-600 dark:text-blue-400"
                                            />
                                        </a>
                                    </div>
                                @else
                                    <p
                                        class="mt-2 text-zinc-500 dark:text-zinc-400"
                                    >
                                        {{ __('Not provided') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Documents --}}
                    <div
                        class="space-y-6 rounded-lg bg-zinc-100 p-6 text-sm dark:bg-zinc-900"
                    >
                        <flux:heading size="lg">
                            {{ __('Documents') }}
                        </flux:heading>

                        @if ($application->documents && $application->documents->count() > 0)
                            <div class="space-y-2">
                                @foreach ($application->documents as $document)
                                    <div
                                        class="flex items-center justify-between rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-800"
                                    >
                                        <div class="flex items-center gap-3">
                                            <flux:icon
                                                name="{{ $document->type->getIcon() }}"
                                                color="{{ $document->type->getColor() }}"
                                            />
                                            <div>
                                                <div
                                                    class="text-sm font-medium text-zinc-900 dark:text-zinc-100"
                                                >
                                                    {{ $document->title }}
                                                </div>
                                                <div
                                                    class="text-xs text-zinc-500 dark:text-zinc-400"
                                                >
                                                    {{ $document->type->getLabel() }}
                                                </div>
                                            </div>
                                        </div>
                                        <flux:button
                                            size="sm"
                                            variant="ghost"
                                            icon="cloud-arrow-down"
                                            wire:click="downloadDocument({{ $document->id }})"
                                        ></flux:button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div
                                class="text-center py-8 text-zinc-500 dark:text-zinc-400"
                            >
                                {{ __('No documents attached') }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right Column (1/3 width) --}}
                <div class="space-y-6 lg:col-span-1">
                    {{-- Status --}}
                    <div
                        class="space-y-6 rounded-lg bg-zinc-100 p-6 text-sm dark:bg-zinc-900"
                    >
                        <flux:heading size="lg">
                            {{ __('Status, Priority & Tags') }}
                        </flux:heading>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <flux:label>{{ __('Status') }}</flux:label>
                                <div class="mt-2">
                                    <flux:badge
                                        color="{{ $application->status->getColor() }}"
                                        size="sm"
                                    >
                                        {{ $application->status->getLabel() }}
                                    </flux:badge>
                                </div>
                            </div>

                            <div>
                                <flux:label>{{ __('Priority') }}</flux:label>
                                <div class="mt-2">
                                    @if ($application->priority)
                                        <flux:badge
                                            color="{{ $application->priority->getColor() }}"
                                            size="sm"
                                        >
                                            {{ $application->priority->getLabel() }}
                                        </flux:badge>
                                    @else
                                        <p
                                            class="text-zinc-500 dark:text-zinc-400"
                                        >
                                            {{ __('Not set') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div>
                            <flux:label>{{ __('Tags') }}</flux:label>
                            @if ($application->tags && count($application->tags) > 0)
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($application->tags as $tag)
                                        <flux:badge color="blue" size="sm">
                                            {{ $tag }}
                                        </flux:badge>
                                    @endforeach
                                </div>
                            @else
                                <p
                                    class="mt-2 text-zinc-500 dark:text-zinc-400"
                                >
                                    {{ __('No tags') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Timeline --}}
                    <div
                        class="space-y-6 rounded-lg bg-zinc-100 p-6 text-sm dark:bg-zinc-900"
                    >
                        <flux:heading size="lg">
                            {{ __('Timeline') }}
                        </flux:heading>

                        <div class="space-y-4">
                            @if ($application->created_at)
                                <div class="flex items-start gap-4">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-zinc-200 dark:bg-zinc-700"
                                    >
                                        <flux:icon.calendar
                                            class="size-4 text-zinc-600 dark:text-zinc-400"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="font-medium text-zinc-900 dark:text-zinc-100"
                                        >
                                            {{ $application->created_at->format('d/m/Y') }}
                                        </div>
                                        <div
                                            class="text-sm text-zinc-600 dark:text-zinc-400"
                                        >
                                            {{ __('Created') }}
                                            {{ $application->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($application->application_date)
                                <div class="flex items-start gap-4">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-500"
                                    >
                                        <flux:icon.paper-airplane
                                            class="size-4 text-white"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="font-medium text-zinc-900 dark:text-zinc-100"
                                        >
                                            {{ $application->application_date->format('d/m/Y') }}
                                        </div>
                                        <div
                                            class="text-sm text-zinc-600 dark:text-zinc-400"
                                        >
                                            {{ __('Applied') }}
                                            {{ $application->application_date->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($application->screening_date)
                                <div class="flex items-start gap-4">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-500"
                                    >
                                        <flux:icon.shield-check
                                            class="size-4 text-white"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="font-medium text-zinc-900 dark:text-zinc-100"
                                        >
                                            {{ $application->screening_date->format('d/m/Y') }}
                                        </div>
                                        <div
                                            class="text-sm text-zinc-600 dark:text-zinc-400"
                                        >
                                            {{ __('Screening') }}
                                            {{ $application->screening_date->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($application->interview_date)
                                <div class="flex items-start gap-4">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-500"
                                    >
                                        <flux:icon.calendar
                                            class="size-4 text-white"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="font-medium text-zinc-900 dark:text-zinc-100"
                                        >
                                            {{ $application->interview_date->format('d/m/Y') }}
                                        </div>
                                        <div
                                            class="text-sm text-zinc-600 dark:text-zinc-400"
                                        >
                                            {{ __('Interview') }}
                                            {{ $application->interview_date->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($application->technical_test_date)
                                <div class="flex items-start gap-4">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-yellow-500"
                                    >
                                        <flux:icon.document-text
                                            class="size-4 text-white"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="font-medium text-zinc-900 dark:text-zinc-100"
                                        >
                                            {{ $application->technical_test_date->format('d/m/Y') }}
                                        </div>
                                        <div
                                            class="text-sm text-zinc-600 dark:text-zinc-400"
                                        >
                                            {{ __('Technical Test') }}
                                            {{ $application->technical_test_date->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($application->final_interview_date)
                                <div class="flex items-start gap-4">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-pink-500"
                                    >
                                        <flux:icon.calendar
                                            class="size-4 text-white"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="font-medium text-zinc-900 dark:text-zinc-100"
                                        >
                                            {{ $application->final_interview_date->format('d/m/Y') }}
                                        </div>
                                        <div
                                            class="text-sm text-zinc-600 dark:text-zinc-400"
                                        >
                                            {{ __('Final Interview') }}
                                            {{ $application->final_interview_date->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($application->offer_date)
                                <div class="flex items-start gap-4">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-500"
                                    >
                                        <flux:icon.calendar
                                            class="size-4 text-white"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="font-medium text-zinc-900 dark:text-zinc-100"
                                        >
                                            {{ $application->offer_date->format('d/m/Y') }}
                                        </div>
                                        <div
                                            class="text-sm text-zinc-600 dark:text-zinc-400"
                                        >
                                            {{ __('Offer') }}
                                            {{ $application->offer_date->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($application->accepted_date)
                                <div class="flex items-start gap-4">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-500"
                                    >
                                        <flux:icon.shield-check
                                            class="size-4 text-white"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="font-medium text-zinc-900 dark:text-zinc-100"
                                        >
                                            {{ $application->accepted_date->format('d/m/Y') }}
                                        </div>
                                        <div
                                            class="text-sm text-zinc-600 dark:text-zinc-400"
                                        >
                                            {{ __('Accepted') }}
                                            {{ $application->accepted_date->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($application->rejected_date)
                                <div class="flex items-start gap-4">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-500"
                                    >
                                        <flux:icon.calendar
                                            class="size-4 text-white"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="font-medium text-zinc-900 dark:text-zinc-100"
                                        >
                                            {{ $application->rejected_date->format('d/m/Y') }}
                                        </div>
                                        <div
                                            class="text-sm text-zinc-600 dark:text-zinc-400"
                                        >
                                            {{ __('Rejected') }}
                                            {{ $application->rejected_date->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($application->withdrawn_date)
                                <div class="flex items-start gap-4">
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-rose-500"
                                    >
                                        <flux:icon.calendar
                                            class="size-4 text-white"
                                        />
                                    </div>
                                    <div class="flex-1">
                                        <div
                                            class="font-medium text-zinc-900 dark:text-zinc-100"
                                        >
                                            {{ $application->withdrawn_date->format('d/m/Y') }}
                                        </div>
                                        <div
                                            class="text-sm text-zinc-600 dark:text-zinc-400"
                                        >
                                            {{ __('Withdrawn') }}
                                            {{ $application->withdrawn_date->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </flux:tab.panel>

        {{-- Job Description Tab Panel --}}
        @if ($application->job_description)
            <flux:tab.panel name="job-description">
                <div class="space-y-6">
                    <div
                        class="space-y-6 rounded-lg bg-zinc-100 p-6 text-sm dark:bg-zinc-900"
                    >
                        <flux:heading size="lg">
                            {{ __('Job Description') }}
                        </flux:heading>
                        <div
                            class="prose prose-sm dark:prose-invert max-w-none"
                        >
                            {!! $application->job_description !!}
                        </div>
                    </div>
                </div>
            </flux:tab.panel>
        @endif

        {{-- Notes Tab Panel --}}
        @if ($application->notes)
            <flux:tab.panel name="notes">
                <div class="space-y-6">
                    <div
                        class="space-y-6 rounded-lg bg-zinc-100 p-6 text-sm dark:bg-zinc-900"
                    >
                        <flux:heading size="lg">
                            {{ __('Notes') }}
                        </flux:heading>
                        <div
                            class="prose prose-sm dark:prose-invert max-w-none"
                        >
                            {!! $application->notes !!}
                        </div>
                    </div>
                </div>
            </flux:tab.panel>
        @endif

        {{-- AI Tab Panel --}}
        <flux:tab.panel name="ai">
            <div class="space-y-6">
                <flux:tab.group>
                    <flux:tabs variant="segmented">
                        <flux:tab name="role-analysis">
                            {{ __('Role Analysis') }}
                        </flux:tab>
                        <flux:tab name="profile-matching">
                            {{ __('Profile Matching') }}
                        </flux:tab>
                        <flux:tab name="cover-letter">
                            {{ __('Cover Letter Inspiration') }}
                        </flux:tab>
                    </flux:tabs>

                    <flux:tab.panel name="role-analysis">
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
                    </flux:tab.panel>

                    <flux:tab.panel name="profile-matching">
                        <div class="space-y-6">
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
                                                    <flux:chart.line field="resume" class="text-blue-500" curve="none" />
                                                    <flux:chart.point field="resume" class="text-blue-500" r="5" stroke-width="2" />
                                                    <flux:chart.line field="jd" class="text-violet-500" curve="none" />
                                                    <flux:chart.point field="jd" class="text-violet-500" r="5" stroke-width="2" />

                                                    <flux:chart.axis axis="x" field="keyword">
                                                        <flux:chart.axis.tick />
                                                        <flux:chart.axis.line />
                                                    </flux:chart.axis>

                                                    <flux:chart.axis axis="y" tick-start="0" tick-end="{{ $yAxisMax }}" :format="[
                                                        'minimumFractionDigits' => 0,
                                                        'maximumFractionDigits' => 0,
                                                    ]">
                                                        <flux:chart.axis.grid />
                                                        <flux:chart.axis.tick />
                                                    </flux:chart.axis>

                                                    {{-- Tooltips removed as requested --}}
                                                </flux:chart.svg>
                                            </flux:chart.viewport>

                                            <div class="flex justify-center gap-4 pt-4">
                                                <flux:chart.legend label="{{ __('Resume') }}">
                                                    <flux:chart.legend.indicator class="bg-blue-400" />
                                                </flux:chart.legend>

                                                <flux:chart.legend label="{{ __('Job Description') }}">
                                                    <flux:chart.legend.indicator class="bg-violet-400" />
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
                    </flux:tab.panel>

                    <flux:tab.panel name="cover-letter">
                        <div class="space-y-6">
                            @if ($coverLetter && $application->documents->isNotEmpty())
                                <div class="flex items-center justify-between">
                                    <flux:heading size="lg">
                                        {{ __('Cover Letter Inspiration') }}
                                    </flux:heading>
                                    <div
                                        class="flex items-center gap-2"
                                        x-data="{ cover: @entangle('coverLetter') }"
                                    >
                                        <flux:button
                                            variant="outline"
                                            size="sm"
                                            icon="clipboard"
                                            @click="(function(text){ if(!text) return; if (window.navigator && window.navigator.clipboard && window.navigator.clipboard.writeText){ window.navigator.clipboard.writeText(text).then(()=>{ $flux.toast({ heading: 'Copied', text: 'Cover letter copied to clipboard.', variant: 'success' }); }).catch(()=>{ const ta=document.createElement('textarea'); ta.value=text; ta.setAttribute('readonly',''); ta.style.position='absolute'; ta.style.left='-9999px'; document.body.appendChild(ta); ta.select(); try{ document.execCommand('copy'); $flux.toast({ heading: 'Copied', text: 'Cover letter copied to clipboard.', variant: 'success' }); } catch(e){ $flux.toast({ heading: 'Copy failed', text: 'Please copy manually.', variant: 'danger' }); } document.body.removeChild(ta); }); } else { const ta=document.createElement('textarea'); ta.value=text; ta.setAttribute('readonly',''); ta.style.position='absolute'; ta.style.left='-9999px'; document.body.appendChild(ta); ta.select(); try{ document.execCommand('copy'); $flux.toast({ heading: 'Copied', text: 'Cover letter copied to clipboard.', variant: 'success' }); } catch(e){ $flux.toast({ heading: 'Copy failed', text: 'Please copy manually.', variant: 'danger' }); } document.body.removeChild(ta); } })(cover)"
                                            x-bind:disabled="! (cover && cover.length)"
                                        >
                                            {{ __('Copy') }}
                                        </flux:button>
                                        <flux:button
                                            variant="primary"
                                            size="sm"
                                            icon="arrow-down-tray"
                                            wire:click="downloadCoverLetter"
                                        >
                                            {{ __('Download') }}
                                        </flux:button>
                                        <flux:button
                                            variant="primary"
                                            icon="sparkles"
                                            wire:click="generateCoverLetter"
                                            size="sm"
                                        >
                                            @if ($coverLetter)
                                                {{ __('Regenerate') }}
                                            @else
                                                {{ __('Generate') }}
                                            @endif
                                        </flux:button>
                                    </div>
                                </div>
                            @else
                                <flux:heading size="lg">
                                    {{ __('Cover Letter Inspiration') }}
                                </flux:heading>
                                <div
                                    class="space-y-6 rounded-lg bg-zinc-100 px-6 text-sm text-center dark:bg-zinc-900 py-16"
                                >
                                    <p class="text-zinc-600 dark:text-zinc-400">
                                        {{ __('Add a job description and cv to generate the cover letter.') }}
                                    </p>
                                </div>
                            @endif

                            @if ($coverLetter)
                                <flux:card>
                                    <div
                                        class="text-sm text-zinc-600 dark:text-zinc-400 mb-2"
                                    >
                                        {{ $application->job_title }} @
                                        {{ $application->organisation }}
                                    </div>
                                    <div
                                        class="not-prose text-sm text-zinc-800 dark:text-zinc-200 leading-relaxed whitespace-pre-line"
                                    >
                                        {{ $coverLetter }}
                                    </div>
                                </flux:card>
                            @endif
                        </div>
                    </flux:tab.panel>
                </flux:tab.group>
            </div>
        </flux:tab.panel>
    </flux:tab.group>
</div>

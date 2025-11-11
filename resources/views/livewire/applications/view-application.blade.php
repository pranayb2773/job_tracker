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
                            <flux:subheading class="mt-1">
                                Analyze job descriptions to identify key
                                requirements and optimize your application
                                strategy
                            </flux:subheading>

                            @if (empty($application->job_description))
                                <div
                                    class="space-y-6 rounded-lg bg-zinc-100 px-6 text-sm text-center dark:bg-zinc-900 py-16"
                                >
                                    <p class="text-zinc-600 dark:text-zinc-400">
                                        {{ __('Add a job description to run role analysis.') }}
                                    </p>
                                </div>
                            @else
                                <div class="flex items-center gap-3">
                                    @if ($roleAnalysis)
                                        <flux:button
                                            wire:click="downloadAnalysis"
                                            variant="primary"
                                            icon="arrow-down-tray"
                                        >
                                            {{ __('Download PDF') }}
                                        </flux:button>
                                    @endif

                                    <flux:button
                                        wire:click="analyzeRole"
                                        variant="primary"
                                        icon-trailing="sparkles"
                                    >
                                        @if ($roleAnalysis)
                                            {{ __('Regenerate Analysis') }}
                                        @else
                                            {{ __('Generate Analysis') }}
                                        @endif
                                    </flux:button>
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
                        <div
                            class="space-y-6 rounded-lg bg-zinc-100 p-6 text-sm dark:bg-zinc-900"
                        >
                            <flux:heading size="lg">
                                {{ __('Profile Matching') }}
                            </flux:heading>
                            <p class="text-zinc-600 dark:text-zinc-400">
                                {{ __('Coming soon: Compare your profile with the role requirements and highlight strengths and gaps.') }}
                            </p>
                        </div>
                    </flux:tab.panel>

                    <flux:tab.panel name="cover-letter">
                        <div
                            class="space-y-6 rounded-lg bg-zinc-100 p-6 text-sm dark:bg-zinc-900"
                        >
                            <flux:heading size="lg">
                                {{ __('Cover Letter Inspiration') }}
                            </flux:heading>
                            <p class="text-zinc-600 dark:text-zinc-400">
                                {{ __('Coming soon: Generate tailored cover letter inspiration based on the job description.') }}
                            </p>
                        </div>
                    </flux:tab.panel>
                </flux:tab.group>
            </div>
        </flux:tab.panel>
    </flux:tab.group>
</div>

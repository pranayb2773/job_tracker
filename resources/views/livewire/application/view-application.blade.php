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
        <flux:tabs wire:model="activeTab">
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
                    <flux:icon.sparkles class="size-4"/>
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
                        <livewire:application.tabs.role-analysis :application="$application"/>
                    </flux:tab.panel>

                    <flux:tab.panel name="profile-matching">
                        <livewire:application.tabs.profile-matching :application="$application"/>
                    </flux:tab.panel>

                    <flux:tab.panel name="cover-letter">
                        <livewire:application.tabs.cover-letter :application="$application"/>
                    </flux:tab.panel>
                </flux:tab.group>
            </div>
        </flux:tab.panel>
    </flux:tab.group>
</div>

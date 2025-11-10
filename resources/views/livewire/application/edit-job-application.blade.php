@php
    use App\Enums\ApplicationPriority;
    use App\Enums\ApplicationStatus;
    use App\Enums\JobType;
@endphp

<div class="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-6 py-6">
    <!-- Breadcrumbs -->
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
            {{ __('Edit Application') }}
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <!-- Header -->
    <div class="flex items-start justify-between">
        <div>
            <flux:heading size="xl">
                {{ __('Edit Job Application') }}
            </flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Update the details of your job application.') }}
            </flux:text>
        </div>
        <div class="flex items-start gap-3">
            <flux:button wire:click="cancel" variant="ghost" size="sm">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button
                href="{{ route('applications.show', $application) }}"
                variant="outline"
                size="sm"
                icon="eye"
                wire:navigate
            >
                {{ __('View') }}
            </flux:button>
            <flux:button
                wire:click="save"
                variant="primary"
                size="sm"
                icon-trailing="check"
            >
                {{ __('Update') }}
            </flux:button>
        </div>
    </div>

    <!-- Form with Two Column Layout -->
    <form wire:submit="save" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column (2/3 width) -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Basic Information -->
            <div class="space-y-6 rounded-lg bg-zinc-100 p-6 dark:bg-zinc-900">
                <flux:heading size="lg">
                    {{ __('Basic Information') }}
                </flux:heading>

                <div class="space-y-6">
                    <flux:field>
                        <flux:label>
                            {{ __('Job Title') }}
                            <span class="text-red-500 pl-0.5">*</span>
                        </flux:label>
                        <flux:input
                            wire:model="form.job_title"
                            placeholder="e.g., Senior Product Designer"
                        />
                        <flux:error name="form.job_title" />
                    </flux:field>

                    <flux:field>
                        <flux:label>
                            {{ __('Organisation') }}
                            <span class="text-red-500 pl-0.5">*</span>
                        </flux:label>
                        <flux:input
                            wire:model="form.organisation"
                            placeholder="e.g., Acme Inc.."
                        />
                        <flux:error name="form.organisation" />
                    </flux:field>
                </div>
            </div>

            <!-- Job Description -->
            <div class="space-y-6 rounded-lg bg-zinc-100 p-6 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">
                        {{ __('Job Description') }}
                    </flux:heading>
                    <flux:badge size="sm" color="zinc" inset="top bottom">
                        {{ __('Optional') }}
                    </flux:badge>
                </div>
                <flux:field>
                    <flux:editor
                        wire:model="form.job_description"
                        placeholder="Copy and paste the job advert text here..."
                    />
                    <flux:error name="form.job_description" />
                </flux:field>
            </div>

            <!-- Job Details -->
            <div class="space-y-6 rounded-lg bg-zinc-100 p-6 dark:bg-zinc-900">
                <flux:heading size="lg">{{ __('Job Details') }}</flux:heading>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('Location') }}</flux:label>
                        <flux:input
                            wire:model="form.location"
                            placeholder="e.g., London, UK"
                        />
                        <flux:error name="form.location" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Job Type') }}</flux:label>
                        <flux:select
                            wire:model="form.type"
                            variant="listbox"
                            placeholder="Select a type"
                        >
                            @foreach (JobType::cases() as $jobType)
                                <flux:select.option
                                    value="{{ $jobType->value }}"
                                >
                                    {{ $jobType->getLabel() }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="form.type" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Working Model') }}</flux:label>
                        <flux:select
                            wire:model="form.work_arrangement"
                            variant="listbox"
                            placeholder="Select a model"
                        >
                            <flux:select.option value="remote">
                                {{ __('Remote') }}
                            </flux:select.option>
                            <flux:select.option value="hybrid">
                                {{ __('Hybrid') }}
                            </flux:select.option>
                            <flux:select.option value="onsite">
                                {{ __('On-site') }}
                            </flux:select.option>
                        </flux:select>
                        <flux:error name="form.work_arrangement" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Deadline') }}</flux:label>
                        <flux:date-picker wire:model="form.deadline">
                            <x-slot name="trigger">
                                <flux:date-picker.input />
                            </x-slot>
                        </flux:date-picker>
                        <flux:error name="form.deadline" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Salary Range') }}</flux:label>
                        <flux:input
                            wire:model="form.salary_range"
                            placeholder="e.g., £50,000 - £70,000"
                        />
                        <flux:error name="form.salary_range" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Minimum Salary') }}</flux:label>
                        <flux:input
                            type="number"
                            wire:model="form.salary_min"
                            placeholder="50000"
                        />
                        <flux:error name="form.salary_min" />
                    </flux:field>
                </div>
            </div>

            <!-- Source -->
            <div class="space-y-6 rounded-lg bg-zinc-100 p-6 dark:bg-zinc-900">
                <flux:heading size="lg">{{ __('Source') }}</flux:heading>

                <div class="space-y-6">
                    <flux:field>
                        <flux:label>{{ __('Link to job advert') }}</flux:label>
                        <flux:input
                            wire:model="form.job_url"
                            placeholder="https://..."
                        />
                        <flux:error name="form.job_url" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Source type') }}</flux:label>
                        <flux:input
                            wire:model="form.source"
                            placeholder="e.g., LinkedIn, Company Website, Referral"
                        />
                        <flux:error name="form.source" />
                    </flux:field>

                    <flux:field>
                        <flux:label>
                            {{ __('Link to advert in job search website') }}
                        </flux:label>
                        <flux:input
                            wire:model="form.source_url"
                            placeholder="https://..."
                        />
                        <flux:description>
                            {{ __('If found on a job board (LinkedIn, Indeed, etc.)') }}
                        </flux:description>
                        <flux:error name="form.source_url" />
                    </flux:field>
                </div>
            </div>

            <!-- Documents -->
            <div class="space-y-6 rounded-lg bg-zinc-100 p-6 dark:bg-zinc-900">
                <flux:heading size="lg">{{ __('Documents') }}</flux:heading>

                <flux:field>
                    <flux:label>{{ __('Attach documents') }}</flux:label>
                    <flux:description>
                        {{ __('Select existing documents from your library to attach to this application.') }}
                    </flux:description>
                </flux:field>

                <div class="space-y-2">
                    @foreach ($this->availableDocuments as $document)
                        <div
                            x-show="($wire.form.document_ids ?? []).includes({{ $document->id }})"
                            class="flex items-center justify-between rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900"
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
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $document->type->getLabel() }}
                                    </div>
                                </div>
                            </div>
                            <flux:button
                                type="button"
                                @click="$wire.form.document_ids = $wire.form.document_ids.filter(id => id !== {{ $document->id }})"
                                variant="ghost"
                                icon="trash"
                                size="sm"
                            />
                        </div>
                    @endforeach
                </div>

                <div>
                    <flux:modal.trigger name="document-selection">
                        <flux:button
                            type="button"
                            variant="outline"
                            icon-trailing="paper-clip"
                            size="sm"
                        >
                            {{ __('Select file') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            <!-- Notes -->
            <div class="space-y-6 rounded-lg bg-zinc-100 p-6 dark:bg-zinc-900">
                <flux:heading size="lg">{{ __('Notes') }}</flux:heading>
                <flux:field>
                    <flux:editor
                        wire:model="form.notes"
                        placeholder="Add any notes or comments about this application..."
                    />
                    <flux:error name="form.notes" />
                </flux:field>
            </div>
        </div>

        <!-- Right Column (1/3 width) -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Status -->
            <div class="space-y-6 rounded-lg bg-zinc-100 p-6 dark:bg-zinc-900">
                <flux:heading size="lg">{{ __('Status') }}</flux:heading>

                <flux:field>
                    <flux:label>
                        {{ __('Status') }}
                        <span class="text-red-500 pl-0.5">*</span>
                    </flux:label>
                    <flux:select wire:model="form.status" variant="listbox">
                        @foreach (ApplicationStatus::cases() as $status)
                            <flux:select.option value="{{ $status->value }}">
                                {{ $status->getLabel() }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="form.status" />
                </flux:field>

                <flux:field>
                    <flux:label>
                        {{ __('Application date') }}
                        <span class="text-red-500 pl-0.5">*</span>
                    </flux:label>
                    <flux:date-picker wire:model="form.application_date">
                        <x-slot name="trigger">
                            <flux:date-picker.input />
                        </x-slot>
                    </flux:date-picker>
                    <flux:error name="form.application_date" />
                </flux:field>

                @php($currentStatus = $this->form->status ?? null)

                @if ($currentStatus === ApplicationStatus::Screening->value)
                    <flux:field>
                        <flux:label>{{ __('Screening date') }}</flux:label>
                        <flux:date-picker wire:model="form.screening_date">
                            <x-slot name="trigger">
                                <flux:date-picker.input />
                            </x-slot>
                        </flux:date-picker>
                        <flux:error name="form.screening_date" />
                    </flux:field>
                @elseif ($currentStatus === ApplicationStatus::Interview->value)
                    <flux:field>
                        <flux:label>{{ __('Interview date') }}</flux:label>
                        <flux:date-picker wire:model="form.interview_date">
                            <x-slot name="trigger">
                                <flux:date-picker.input />
                            </x-slot>
                        </flux:date-picker>
                        <flux:error name="form.interview_date" />
                    </flux:field>
                @elseif ($currentStatus === ApplicationStatus::TechnicalTest->value)
                    <flux:field>
                        <flux:label>{{ __('Technical test date') }}</flux:label>
                        <flux:date-picker wire:model="form.technical_test_date">
                            <x-slot name="trigger">
                                <flux:date-picker.input />
                            </x-slot>
                        </flux:date-picker>
                        <flux:error name="form.technical_test_date" />
                    </flux:field>
                @elseif ($currentStatus === ApplicationStatus::FinalInterview->value)
                    <flux:field>
                        <flux:label>{{ __('Final interview date') }}</flux:label>
                        <flux:date-picker wire:model="form.final_interview_date">
                            <x-slot name="trigger">
                                <flux:date-picker.input />
                            </x-slot>
                        </flux:date-picker>
                        <flux:error name="form.final_interview_date" />
                    </flux:field>
                @elseif ($currentStatus === ApplicationStatus::Offer->value)
                    <flux:field>
                        <flux:label>{{ __('Offer date') }}</flux:label>
                        <flux:date-picker wire:model="form.offer_date">
                            <x-slot name="trigger">
                                <flux:date-picker.input />
                            </x-slot>
                        </flux:date-picker>
                        <flux:error name="form.offer_date" />
                    </flux:field>
                @elseif ($currentStatus === ApplicationStatus::Accepted->value)
                    <flux:field>
                        <flux:label>{{ __('Accepted date') }}</flux:label>
                        <flux:date-picker wire:model="form.accepted_date">
                            <x-slot name="trigger">
                                <flux:date-picker.input />
                            </x-slot>
                        </flux:date-picker>
                        <flux:error name="form.accepted_date" />
                    </flux:field>
                @elseif ($currentStatus === ApplicationStatus::Rejected->value)
                    <flux:field>
                        <flux:label>{{ __('Rejected date') }}</flux:label>
                        <flux:date-picker wire:model="form.rejected_date">
                            <x-slot name="trigger">
                                <flux:date-picker.input />
                            </x-slot>
                        </flux:date-picker>
                        <flux:error name="form.rejected_date" />
                    </flux:field>
                @elseif ($currentStatus === ApplicationStatus::Withdrawn->value)
                    <flux:field>
                        <flux:label>{{ __('Withdrawn date') }}</flux:label>
                        <flux:date-picker wire:model="form.withdrawn_date">
                            <x-slot name="trigger">
                                <flux:date-picker.input />
                            </x-slot>
                        </flux:date-picker>
                        <flux:error name="form.withdrawn_date" />
                    </flux:field>
                @endif
            </div>

            <!-- Priority & Tags -->
            <div class="space-y-6 rounded-lg bg-zinc-100 p-6 dark:bg-zinc-900">
                <flux:heading size="lg">
                    {{ __('Priority & Tags') }}
                </flux:heading>

                <flux:field>
                    <flux:label>{{ __('Priority') }}</flux:label>
                    <flux:radio.group
                        wire:model="form.priority"
                        variant="pills"
                    >
                        @foreach (ApplicationPriority::cases() as $priority)
                            <flux:radio
                                value="{{ $priority->value }}"
                                label="{{ $priority->getLabel() }}"
                            />
                        @endforeach
                    </flux:radio.group>
                    <flux:error name="form.priority" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Tags') }}</flux:label>
                    <flux:input
                        x-data="{ tag: '' }"
                        x-model="tag"
                        @keydown.enter.prevent="
                            if (tag.trim() && !($wire.form.tags ?? []).includes(tag.trim())) {
                                $wire.form.tags = [...($wire.form.tags ?? []), tag.trim()];
                            }
                            tag = '';
                        "
                        placeholder="Type a tag and press Enter"
                    />
                    <template x-if="($wire.form.tags ?? []).length > 0">
                        <div class="mt-3 flex flex-wrap gap-2">
                            <template
                                x-for="(tag, index) in ($wire.form.tags ?? [])"
                                :key="index"
                            >
                                <flux:badge color="blue" size="sm">
                                    <span x-text="tag"></span>
                                    <button
                                        type="button"
                                        @click="$wire.form.tags = $wire.form.tags.filter((t, i) => i !== index)"
                                        class="ml-1 hover:text-red-500"
                                    >
                                        ×
                                    </button>
                                </flux:badge>
                            </template>
                        </div>
                    </template>

                    <flux:error name="form.tags" />
                </flux:field>
            </div>
        </div>
    </form>

    <!-- Document Selection Modal -->
    <flux:modal name="document-selection" class="w-full max-w-lg">
        <div class="space-y-4">
            <div>
                <flux:heading size="lg">
                    {{ __('Attach Documents') }}
                </flux:heading>
                <flux:text
                    class="mt-1 text-sm text-zinc-600 dark:text-zinc-400"
                >
                    {{ __('Select documents from your library to attach to this application.') }}
                </flux:text>
            </div>

            @if ($this->availableDocuments->isEmpty())
                <div class="py-12 text-center">
                    <div
                        class="mx-auto mb-4 flex size-16 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800"
                    >
                        <flux:icon.document-text
                            class="size-8 text-zinc-400 dark:text-zinc-500"
                        />
                    </div>
                    <flux:heading size="base" class="mb-2">
                        {{ __('No documents available') }}
                    </flux:heading>
                    <flux:text
                        class="mb-4 text-sm text-zinc-500 dark:text-zinc-400"
                    >
                        {{ __('Upload documents in the Documents section to attach them here.') }}
                    </flux:text>
                    <flux:button
                        href="{{ route('documents.list') }}"
                        wire:navigate
                        variant="primary"
                        size="sm"
                        icon="arrow-up-tray"
                    >
                        {{ __('Go to Documents') }}
                    </flux:button>
                </div>
            @else
                <div class="max-h-96 space-y-2 overflow-y-auto">
                    @foreach ($this->availableDocuments as $document)
                        <button
                            type="button"
                            @click="if (!($wire.form.document_ids ?? []).includes({{ $document->id }})) { $wire.form.document_ids = [...($wire.form.document_ids ?? []), {{ $document->id }}] }"
                            class="flex w-full items-center gap-3 rounded-lg border border-zinc-200 bg-white p-3 transition hover:bg-zinc-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-750"
                            :disabled="($wire.form.document_ids ?? []).includes({{ $document->id }})"
                        >
                            <flux:icon
                                name="{{ $document->type->getIcon() }}"
                                color="{{ $document->type->getColor() }}"
                            ></flux:icon>

                            <div class="flex-1 text-left">
                                <div
                                    class="text-sm font-medium text-zinc-900 dark:text-zinc-100"
                                >
                                    {{ $document->title }}
                                </div>
                                <div
                                    class="text-xs text-zinc-500 dark:text-zinc-400"
                                >
                                    {{ $document->type->getLabel() }} •
                                    {{ $document->file_size_formatted }}
                                </div>
                            </div>

                            <template
                                x-if="($wire.form.document_ids ?? []).includes({{ $document->id }})"
                            >
                                <flux:icon.check
                                    class="size-5 shrink-0 text-blue-600 dark:text-blue-400"
                                />
                            </template>
                            <template
                                x-if="! ($wire.form.document_ids ?? []).includes({{ $document->id }})"
                            >
                                <flux:icon.plus
                                    class="size-5 shrink-0 text-zinc-400 dark:text-zinc-500"
                                />
                            </template>
                        </button>
                    @endforeach
                </div>
            @endif

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:icon.information-circle
                        class="size-5 shrink-0 text-blue-600 dark:text-blue-400"
                    />
                    <span
                        class="text-sm text-zinc-600 dark:text-zinc-400"
                        x-text="`${($wire.form.document_ids ?? []).length} ${($wire.form.document_ids ?? []).length === 1 ? 'document' : 'documents'} selected`"
                    ></span>
                </div>
                <div class="flex items-center gap-3">
                    <flux:modal.close>
                        <flux:button variant="ghost">
                            {{ __('Cancel') }}
                        </flux:button>
                    </flux:modal.close>
                    <flux:modal.close>
                        <flux:button variant="primary">
                            {{ __('Done') }}
                        </flux:button>
                    </flux:modal.close>
                </div>
            </div>
        </div>
    </flux:modal>
</div>

@php
    use App\Enums\ApplicationPriority;
    use App\Enums\ApplicationStatus;
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
            {{ __('New Application') }}
        </flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <!-- Header -->
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('New job application') }}</flux:heading>
        <div class="flex items-center gap-3">
            <flux:button wire:click="cancel" variant="ghost">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button
                wire:click="save"
                variant="primary"
                icon-trailing="check"
            >
                {{ __('Create') }}
            </flux:button>
        </div>
    </div>

    <!-- Form with Two Column Layout -->
    <form wire:submit="save" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column (2/3 width) -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Basic Information -->
            <flux:card class="space-y-6">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">
                        {{ __('Basic information') }}
                    </flux:heading>
                    <flux:button
                        type="button"
                        wire:click="openFastTrackModal"
                        variant="ghost"
                        size="sm"
                        icon-trailing="sparkles"
                    >
                        {{ __('AI Extract') }}
                    </flux:button>
                </div>
                <flux:separator />

                <div class="space-y-6">
                    <flux:field>
                        <flux:label>
                            {{ __('Job title') }}
                            <span class="text-red-500">*</span>
                        </flux:label>
                        <flux:input
                            wire:model="form.job_title"
                            placeholder="Enter the job title"
                        />
                        <flux:error name="form.job_title" />
                    </flux:field>

                    <flux:field>
                        <flux:label>
                            {{ __('Organisation') }}
                            <span class="text-red-500">*</span>
                        </flux:label>
                        <flux:input
                            wire:model="form.organisation"
                            placeholder="Enter the organisation or company name"
                        />
                        <flux:error name="form.organisation" />
                    </flux:field>
                </div>
            </flux:card>

            <!-- Job Description -->
            <flux:card class="space-y-6">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">
                        {{ __('Job description') }}
                    </flux:heading>
                    <flux:badge size="sm" color="zinc" inset="top bottom">
                        {{ __('Optional') }}
                    </flux:badge>
                </div>
                <flux:separator />
                <flux:field>
                    <flux:editor
                        wire:model="form.job_description"
                        placeholder="Copy and paste the job advert text here..."
                    />
                    <flux:error name="form.job_description" />
                </flux:field>
            </flux:card>

            <!-- Job Details -->
            <flux:card class="space-y-6">
                <flux:heading size="lg">{{ __('Job details') }}</flux:heading>
                <flux:separator />

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <flux:field>
                        <flux:label>{{ __('Working model') }}</flux:label>
                        <flux:select
                            wire:model="form.work_arrangement"
                            variant="listbox"
                            placeholder="Select a working model"
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
                        <flux:label>{{ __('Salary range') }}</flux:label>
                        <flux:input
                            wire:model="form.salary_range"
                            placeholder="e.g., £50,000 - £70,000"
                        />
                        <flux:error name="form.salary_range" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Minimum salary') }}</flux:label>
                        <flux:input
                            type="number"
                            wire:model="form.salary_min"
                            placeholder="0"
                        />
                        <flux:error name="form.salary_min" />
                    </flux:field>
                </div>
            </flux:card>

            <!-- Source -->
            <flux:card class="space-y-6">
                <flux:heading size="lg">{{ __('Source') }}</flux:heading>
                <flux:separator />

                <div class="space-y-6">
                    <flux:field>
                        <flux:label>{{ __('Link to job advert') }}</flux:label>
                        <flux:input
                            wire:model="form.job_url"
                            placeholder="https://"
                        />
                        <flux:error name="form.job_url" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Source') }}</flux:label>
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
                            placeholder="https://"
                        />
                        <flux:description>
                            {{ __('If found on a job board (LinkedIn, Indeed, etc.)') }}
                        </flux:description>
                        <flux:error name="form.source_url" />
                    </flux:field>
                </div>
            </flux:card>

            <!-- Documents -->
            <flux:card class="space-y-6">
                <flux:heading size="lg">{{ __('Documents') }}</flux:heading>
                <flux:separator />

                <flux:field>
                    <flux:label>{{ __('Attach documents') }}</flux:label>
                    <flux:description>
                        {{ __('Select existing documents from your library to attach to this application.') }}
                    </flux:description>
                </flux:field>

                @if ($this->attachedDocuments->isNotEmpty())
                    <div class="space-y-2">
                        @foreach ($this->attachedDocuments as $document)
                            <div
                                class="flex items-center justify-between rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex size-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-950"
                                    >
                                        <flux:icon.document-text
                                            class="size-5 text-blue-600 dark:text-blue-400"
                                        />
                                    </div>
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
                                    type="button"
                                    wire:click="detachDocument({{ $document->id }})"
                                    variant="ghost"
                                    icon="trash"
                                    size="sm"
                                />
                            </div>
                        @endforeach
                    </div>
                @endif

                <div>
                    <flux:button
                        type="button"
                        wire:click="openDocumentModal"
                        variant="outline"
                        icon-trailing="paper-clip"
                        size="sm"
                    >
                        {{ __('Select file') }}
                    </flux:button>
                </div>
            </flux:card>

            <!-- Notes -->
            <flux:card class="space-y-6">
                <flux:heading size="lg">{{ __('Notes') }}</flux:heading>
                <flux:separator />
                <flux:field>
                    <flux:editor
                        wire:model="form.notes"
                        placeholder="Add any notes or comments about this application..."
                    />
                    <flux:error name="form.notes" />
                </flux:field>
            </flux:card>
        </div>

        <!-- Right Column (1/3 width) - Status Sidebar -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Status -->
            <flux:card class="space-y-6">
                <flux:heading size="lg">{{ __('Status') }}</flux:heading>
                <flux:separator />

                <flux:field>
                    <flux:label>
                        {{ __('Status') }}
                        <span class="text-red-500">*</span>
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
                        <span class="text-red-500">*</span>
                    </flux:label>
                    <flux:date-picker wire:model="form.application_date">
                        <x-slot name="trigger">
                            <flux:date-picker.input />
                        </x-slot>
                    </flux:date-picker>
                    <flux:error name="form.application_date" />
                </flux:field>
            </flux:card>

            <!-- Priority & Tags -->
            <flux:card class="space-y-6">
                <flux:heading size="lg">
                    {{ __('Priority & Tags') }}
                </flux:heading>
                <flux:separator />

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
                        @keydown.enter.prevent="$wire.addTag(tag); tag = ''"
                        placeholder="Type a tag and press Enter"
                    />
                    @if (count($form->tags) > 0)
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($form->tags as $index => $tag)
                                <flux:badge color="blue" size="sm">
                                    {{ $tag }}
                                    <button
                                        type="button"
                                        wire:click="removeTag({{ $index }})"
                                        class="ml-1 hover:text-red-500"
                                    >
                                        ×
                                    </button>
                                </flux:badge>
                            @endforeach
                        </div>
                    @endif

                    <flux:error name="form.tags" />
                </flux:field>
            </flux:card>
        </div>
    </form>

    <!-- Fast Track Modal -->
    <flux:modal
        name="fast-track"
        variant="flyout"
        wire:model="showFastTrackModal"
    >
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('AI Extract from Job Advert') }}
                </flux:heading>
                <flux:text>
                    {{ __('Paste the job advert URL to automatically extract job details.') }}
                </flux:text>
            </div>

            <flux:separator />

            <flux:field>
                <flux:label>{{ __('Job advert URL') }}</flux:label>
                <flux:input wire:model="form.job_url" placeholder="https://" />
                <flux:description>
                    {{ __('Experimental AI. Extract job details from the advert page.') }}
                    {{ __('Always use valid URLs from the organisation\'s website.') }}
                    {{ __('Avoid URLs from job search sites like LinkedIn, Indeed, etc.') }}
                </flux:description>
                <flux:error name="form.job_url" />
            </flux:field>

            <flux:separator />

            <div class="flex items-center justify-between">
                <flux:button wire:click="closeFastTrackModal" variant="ghost">
                    {{ __('Close') }}
                </flux:button>
                <flux:button
                    wire:click="closeFastTrackModal"
                    variant="primary"
                    icon-trailing="sparkles"
                >
                    {{ __('Extract Details') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Document Selection Modal -->
    <flux:modal
        name="document-selection"
        variant="flyout"
        wire:model="showDocumentModal"
    >
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Attach document') }}
                </flux:heading>
                <flux:text>
                    {{ __('Select an existing document to attach it to this application.') }}
                </flux:text>
            </div>

            <flux:separator />

            @if ($this->availableDocuments->isEmpty())
                <div class="py-12 text-center">
                    <div
                        class="mx-auto mb-4 flex size-16 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800"
                    >
                        <flux:icon.document-text class="size-8 text-zinc-400" />
                    </div>
                    <flux:heading size="sm" class="mb-2">
                        {{ __('No documents available') }}
                    </flux:heading>
                    <flux:text class="text-sm text-zinc-500">
                        {{ __('You can upload documents in the Documents section.') }}
                    </flux:text>
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($this->availableDocuments as $document)
                        <button
                            type="button"
                            wire:click="attachDocument({{ $document->id }})"
                            class="flex w-full items-center gap-3 rounded-lg border border-zinc-200 bg-white p-3 transition hover:border-zinc-300 hover:bg-zinc-50 disabled:cursor-not-allowed disabled:opacity-60 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600 dark:hover:bg-zinc-800"
                            @if (in_array($document->id, $form->document_ids)) disabled @endif
                        >
                            <div
                                class="flex size-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-950"
                            >
                                <flux:icon.document-text
                                    class="size-5 text-blue-600 dark:text-blue-400"
                                />
                            </div>
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

                            @if (in_array($document->id, $form->document_ids))
                                <flux:badge color="green" size="sm">
                                    {{ __('Attached') }}
                                </flux:badge>
                            @else
                                <flux:icon.plus class="size-5 text-zinc-400" />
                            @endif
                        </button>
                    @endforeach
                </div>
            @endif

            <flux:separator />

            <div class="flex justify-end">
                <flux:button wire:click="closeDocumentModal" variant="ghost">
                    {{ __('Close') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

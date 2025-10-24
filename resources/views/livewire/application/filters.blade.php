@php
    use App\Enums\ApplicationPriority;
    use App\Enums\ApplicationStatus;
@endphp

<div
    class="border border-collapse border-b-0 rounded-t border-zinc-800/10 dark:border-white/20"
>
    <div
        class="flex flex-col md:flex-row items-start md:items-center md:justify-between gap-y-4 md:gap-x-4 p-4 sm:px-6"
    >
        <div class="flex shrink-0 items-center gap-x-4">
            <div
                class="flex shrink-0 items-center gap-3 justify-end"
                x-show="$wire.selectedApplicationIds.length"
            >
                <flux:dropdown position="bottom" align="start">
                    <flux:button
                        size="sm"
                        icon="ellipsis-horizontal"
                        inset="top bottom"
                    >
                        Bulk actions
                    </flux:button>
                    <flux:menu>
                        <flux:modal.trigger name="delete-applications-bulk">
                            <flux:menu.item icon="trash" variant="danger">
                                Delete selected
                            </flux:menu.item>
                        </flux:modal.trigger>
                    </flux:menu>
                </flux:dropdown>
                <x-confirmation-modal
                    name="delete-applications-bulk"
                    heading="Delete selected applications?"
                    click-event="deleteApplicationsBulk"
                >
                    <x-slot:subHeading>
                        <p>Are you sure you would like to do this?</p>
                        <p>This action cannot be reversed.</p>
                    </x-slot>
                </x-confirmation-modal>
            </div>
        </div>
        <div class="md:ms-auto flex items-center align-middle gap-x-4">
            <flux:input
                wire:model.live.debounce.250ms="search"
                icon="magnifying-glass"
                size="sm"
                placeholder="Search applications"
                clearable
            />
            <flux:dropdown>
                <flux:button icon="funnel" size="sm" icon:class="text-zinc-400">
                    Filters
                    <x-slot name="iconTrailing">
                        <flux:badge size="sm" class="-mr-1">
                            <span
                                x-text="$wire.activeFilterCount"
                                class="tabular-nums"
                            >
                                &nbsp;
                            </span>
                        </flux:badge>
                    </x-slot>
                </flux:button>
                <flux:popover class="max-w-[18rem] flex flex-col gap-4 md:w-96">
                    <flux:select
                        wire:model.live="filters.status"
                        size="sm"
                        variant="listbox"
                        multiple
                        searchable
                        indicator="checkbox"
                        clearable
                        placeholder="Choose application status..."
                        label="Application Status"
                    >
                        @foreach (ApplicationStatus::cases() as $applicationStatus)
                            <flux:select.option
                                value="{{ $applicationStatus->value }}"
                            >
                                {{ $applicationStatus->getLabel() }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:radio.group
                        wire:model.live="filters.priority"
                        size="sm"
                        variant="pills"
                        label="Application Priority"
                    >
                        @foreach (ApplicationPriority::cases() as $applicationPriority)
                            <flux:radio
                                value="{{ $applicationPriority->value }}"
                            >
                                {{ $applicationPriority->getLabel() }}
                            </flux:radio>
                        @endforeach
                    </flux:radio.group>
                    <flux:date-picker
                        wire:model.live="filters.application_date"
                        placeholder="Choose application date..."
                        size="sm"
                        clearable
                        label="Application Date"
                    />
                    <flux:separator variant="subtle" />
                    <flux:button
                        variant="subtle"
                        size="sm"
                        class="justify-center -m-2 !px-2"
                        wire:click="$set('filters', [])"
                    >
                        Clear all
                    </flux:button>
                </flux:popover>
            </flux:dropdown>
        </div>
    </div>
    <div
        class="flex flex-col justify-between gap-y-1 bg-zinc-50 dark:bg-zinc-600/40 px-3 py-2 sm:flex-row sm:items-center sm:px-6 sm:py-1.5"
        x-bind:hidden="!$wire.selectedApplicationIds.length"
        x-show="$wire.selectedApplicationIds.length"
    >
        <div class="flex gap-x-3">
            <span
                class="text-sm font-medium leading-6 text-zinc-800 dark:text-white"
                x-text="
                    window.pluralize(
                        '1 record selected|:count records selected',
                        $wire.selectedApplicationIds.length,
                        { count: $wire.selectedApplicationIds.length },
                    )
                "
            ></span>
        </div>
        <div class="flex gap-x-3">
            <flux:text
                @click="selectAll"
                color="amber"
                size="sm"
                class="cursor-pointer underline hover:text-amber-500/90"
                x-text="`Select All ${$wire.applicationIds.length}`"
                x-bind:hidden="$wire.selectedApplicationIds.length === $wire.applicationIds.length"
            >
                Select All
            </flux:text>
            <flux:text
                @click="deselectAll"
                color="red"
                size="sm"
                class="cursor-pointer underline hover:text-red-500/90"
            >
                Deselect All
            </flux:text>
        </div>
    </div>
</div>

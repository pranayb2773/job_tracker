@php
    use App\Enums\DocumentType;
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
                x-show="$wire.selectedDocumentIds.length"
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
                        <flux:modal.trigger name="delete-documents-bulk">
                            <flux:menu.item icon="trash" variant="danger">
                                Delete selected
                            </flux:menu.item>
                        </flux:modal.trigger>
                    </flux:menu>
                </flux:dropdown>
                <x-confirmation-modal
                    name="delete-documents-bulk"
                    heading="Delete selected documents?"
                    click-event="deleteSelectedDocuments"
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
                placeholder="Search documents"
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
                        wire:model.live="filters.type"
                        size="sm"
                        variant="listbox"
                        multiple
                        searchable
                        indicator="checkbox"
                        clearable
                        placeholder="Choose document type..."
                        label="Document Type"
                    >
                        @foreach (DocumentType::cases() as $documentType)
                            <flux:select.option
                                value="{{ $documentType->value }}"
                            >
                                {{ $documentType->getLabel() }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
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
        x-bind:hidden="!$wire.selectedDocumentIds.length"
        x-show="$wire.selectedDocumentIds.length"
    >
        <div class="flex gap-x-3">
            <span
                class="text-sm font-medium leading-6 text-zinc-800 dark:text-white"
                x-text="
                    window.pluralize(
                        '1 record selected|:count records selected',
                        $wire.selectedDocumentIds.length,
                        { count: $wire.selectedDocumentIds.length },
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
                x-text="`Select All ${$wire.documentIds.length}`"
                x-bind:hidden="$wire.selectedDocumentIds.length === $wire.documentIds.length"
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

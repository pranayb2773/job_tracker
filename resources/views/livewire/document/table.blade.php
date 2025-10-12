<div
    class="relative overflow-x-auto border border-collapse border-zinc-800/10 dark:border-white/20"
>
    <table
        class="[:where(&)]:min-w-full table-fixed text-zinc-800 divide-y divide-zinc-800/10 dark:divide-white/20 whitespace-nowrap [&_dialog]:whitespace-normal [&_[popover]]:whitespace-normal"
    >
        <flux:table.columns class="bg-zinc-50 dark:bg-zinc-600/40">
            <flux:table.column class="!pl-4">
                <div>
                    <flux:checkbox
                        x-ref="checkbox"
                        @change="handleCheck"
                    ></flux:checkbox>
                </div>
            </flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortCol === 'title'"
                :direction="$sortDirection"
                wire:click="sortBy('title')"
            >
                Name
            </flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortCol === 'type'"
                :direction="$sortDirection"
                wire:click="sortBy('type')"
            >
                Type
            </flux:table.column>
            <flux:table.column>Mime Type</flux:table.column>
            <flux:table.column>File Size</flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortCol === 'date'"
                :direction="$sortDirection"
                wire:click="sortBy('date')"
            >
                Modified At
            </flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->documents as $document)
                <flux:table.row :key="$document->id">
                    <flux:table.cell class="!pl-4 size-2">
                        <flux:checkbox
                            wire:model="selectedUserIds"
                            value="{{ $document->id }}"
                        ></flux:checkbox>
                    </flux:table.cell>

                    <flux:table.cell class="min-w-6 w-1/4">
                        <div class="flex items-center gap-2">
                            <flux:icon
                                name="{{ $document->type->getIcon() }}"
                            ></flux:icon>
                            <span>{{ $document->title }}</span>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell class="min-w-6 w-1/4">
                        {{ $user->type }}
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $document->file_mime_type }}
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $document->file_size }}
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $document->updated_at->format('F j, Y g:i A') }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:dropdown
                            position="bottom"
                            align="end"
                            offset="-15"
                            class="px-3"
                        >
                            <flux:button
                                variant="ghost"
                                size="sm"
                                icon="ellipsis-horizontal"
                                inset="top bottom"
                            ></flux:button>
                            <flux:menu>
                                <flux:menu.item icon="cloud-arrow-down">
                                    Download
                                </flux:menu.item>
                                <flux:modal.trigger
                                    name="{{ 'delete-document-' . $document->id }}"
                                >
                                    <flux:menu.item
                                        icon="trash"
                                        variant="danger"
                                    >
                                        Delete
                                    </flux:menu.item>
                                </flux:modal.trigger>
                            </flux:menu>
                        </flux:dropdown>
                        <x-confirmation-modal
                            name="delete-document-{{ $document->id }}"
                            heading="Delete Document?"
                            click-event="deleteDocument({{ $document->id }})"
                        >
                            <x-slot:subHeading>
                                <p>
                                    You're about to delete
                                    <b><i>{{ $document->title }}</i></b>
                                    user.
                                </p>
                                <p>This action cannot be reversed.</p>
                            </x-slot>
                        </x-confirmation-modal>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center py-4">
                        <div
                            class="flex flex-col items-center justify-center space-y-2 text-zinc-500 dark:text-zinc-400"
                        >
                            <flux:icon.x-circle
                                variant="solid"
                                class="size-8"
                            ></flux:icon.x-circle>
                            <flux:text class="flex align-middle">
                                No documents found
                            </flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </table>
</div>

@if ($this->documents)
    <flux:pagination :paginator="$this->documents" />
@endif

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
                        :disabled="$this->applications->isEmpty()"
                    ></flux:checkbox>
                </div>
            </flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortCol === 'title'"
                :direction="$sortDirection"
                wire:click="sortBy('title')"
            >
                Job Title
            </flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortCol === 'organisation'"
                :direction="$sortDirection"
                wire:click="sortBy('organisation')"
            >
                Organisation
            </flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortCol === 'status'"
                :direction="$sortDirection"
                wire:click="sortBy('status')"
            >
                Status
            </flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortCol === 'application_date'"
                :direction="$sortDirection"
                wire:click="sortBy('application_date')"
            >
                Date Applied
            </flux:table.column>
            <flux:table.column>Tags</flux:table.column>
            <flux:table.column
                sortable
                :sorted="$sortCol === 'priority'"
                :direction="$sortDirection"
                wire:click="sortBy('priority')"
            >
                Priority
            </flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->applications as $application)
                <flux:table.row :key="$application->id">
                    <flux:table.cell class="!pl-4 size-2">
                        <flux:checkbox
                            wire:model="selectedApplicationIds"
                            value="{{ $application->id }}"
                        ></flux:checkbox>
                    </flux:table.cell>

                    <flux:table.cell class="min-w-6 w-1/4">
                        <a wire:navigate>
                            {{ $application->job_title }}
                        </a>
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
                        {{ $application->application_date->diffForHumans() }}
                    </flux:table.cell>

                    <flux:table.cell>
                        @foreach ($application->tags as $tag)
                            <flux:badge size="sm" color="zinc" variant="pill">
                                {{ $tag }}
                            </flux:badge>
                        @endforeach
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge
                            size="sm"
                            :color="$application->priority->getColor()"
                            variant="pill"
                        >
                            {{ $application->priority->getLabel() }}
                        </flux:badge>
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
                                <flux:menu.item
                                    icon="pencil"
                                    :href="route('applications.edit', $application)"
                                    wire:navigate
                                >
                                    Edit
                                </flux:menu.item>
                                <flux:menu.item
                                    icon="cloud-arrow-down"
                                    wire:click="downloadDocument({{ $application->id }})"
                                >
                                    Download
                                </flux:menu.item>
                                <flux:modal.trigger
                                    name="{{ 'delete-application-' . $application->id }}"
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
                            name="delete-application-{{ $application->id }}"
                            heading="Delete Application?"
                            click-event="deleteApplication({{ $application->id }})"
                        >
                            <x-slot:subHeading>
                                <p>
                                    You're about to delete
                                    <b><i>{{ $application->job_title }}</i></b>
                                    at
                                    <b>
                                        <i>{{ $application->organisation }}</i>
                                    </b>
                                    .
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
                                No applications found
                            </flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </table>
</div>

@if ($this->applications)
    <flux:pagination :paginator="$this->applications" />
@endif

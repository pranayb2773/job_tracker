@props([
    'name' => '',
    'heading' => null,
    'subHeading' => null,
    'clickEvent' => null,
])

<flux:modal :name="$name" class="md:w-96">
    <div class="space-y-6">
        <div class="sm:flex sm:items-start">
            <div
                class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10"
            >
                <svg
                    class="size-6 text-red-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    aria-hidden="true"
                    data-slot="icon"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"
                    />
                </svg>
            </div>

            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <flux:heading size="lg">{{ $heading }}</flux:heading>
                <flux:text class="mt-2 space-y-2">
                    {{ $subHeading }}
                </flux:text>
            </div>
        </div>

        <div class="flex gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button>Cancel</flux:button>
            </flux:modal.close>

            <flux:button
                type="button"
                variant="danger"
                wire:click="{{ $clickEvent }}"
            >
                Delete
            </flux:button>
        </div>
    </div>
</flux:modal>

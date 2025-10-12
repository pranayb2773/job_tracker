@props([
    'name' => '',
    'heading' => null,
    'subHeading' => null,
    'clickEvent' => null,
])

<flux:modal :name="$name" class="md:w-[32rem]">
    <div class="space-y-6 text-center">
        <div class="flex flex-col items-center space-y-4">
            <div
                class="flex size-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20"
            >
                <flux:icon.trash
                    variant="solid"
                    class="size-8 text-red-600 dark:text-red-500"
                />
            </div>

            <div class="space-y-2">
                <flux:heading
                    size="xl"
                    class="text-zinc-900 dark:text-white font-semibold"
                >
                    {{ $heading }}
                </flux:heading>
                <div
                    class="text-base text-zinc-600 dark:text-zinc-400 space-y-2"
                >
                    {{ $subHeading }}
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <div class="flex-1">
                <flux:modal.close>
                    <flux:button class="w-full">Cancel</flux:button>
                </flux:modal.close>
            </div>

            <flux:button
                type="button"
                variant="danger"
                class="flex-1"
                wire:click="{{ $clickEvent }}"
            >
                Delete
            </flux:button>
        </div>
    </div>
</flux:modal>

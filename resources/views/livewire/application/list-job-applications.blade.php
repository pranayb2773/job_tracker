<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <!-- Breadcrumbs -->
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('dashboard') }}">
            {{ __('Home') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('Applications') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <!-- Title and Create button -->
    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Applications') }}</flux:heading>
        <flux:button
            href="{{ route('applications.create') }}"
            wire:navigate
            icon-trailing="plus"
            size="sm"
            variant="primary"
        >
            {{ __('Create Application') }}
        </flux:button>
    </div>

    <!-- Filters and Table -->
    <div x-data="checkAll">
        @include('livewire.application.filters')
        @include('livewire.application.table')
    </div>
</div>

@script
    <script>
        Alpine.data('checkAll', () => {
            return {
                init() {
                    this.$wire.$watch('selectedApplicationIds', () => {
                        this.updateCheckAllState();
                    });

                    this.$wire.$watch('applicationIdsOnPage', () => {
                        this.updateCheckAllState();
                    });

                    this.$wire.$watch('applicationIds', (newApplications) => {
                        if (!newApplications.length) {
                            this.$refs.checkbox.checked = false; // Remove checked
                            this.$refs.checkbox.indeterminate = false;
                        }
                    });
                },

                updateCheckAllState() {
                    if (this.pageIsSelected()) {
                        // If the page is fully selected
                        this.$refs.checkbox.checked = true; // Set checked
                        this.$refs.checkbox.indeterminate = false;
                    } else if (this.pageIsEmpty()) {
                        // If the page is empty
                        this.$refs.checkbox.checked = false; // Remove checked
                        this.$refs.checkbox.indeterminate = false;
                    } else {
                        // If the page is partially selected (indeterminate state)
                        this.$refs.checkbox.checked = false;
                        this.$refs.checkbox.indeterminate = true;
                    }
                },

                pageIsSelected() {
                    return this.$wire.applicationIdsOnPage.every((id) =>
                        this.$wire.selectedApplicationIds.includes(id),
                    );
                },

                pageIsEmpty() {
                    return this.$wire.selectedApplicationIds.length === 0;
                },

                handleCheck(e) {
                    e.target.hasAttribute('data-checked')
                        ? this.selectAllOnPage()
                        : this.deselectAllOnPage();
                },

                selectAllOnPage() {
                    this.$wire.applicationIdsOnPage.forEach((id) => {
                        if (this.$wire.selectedApplicationIds.includes(id))
                            return;

                        this.$wire.selectedApplicationIds.push(id);
                    });

                    console.log('applicationIds', this.$wire.applicationIds);
                    console.log(
                        'selectedApplicationIds',
                        this.$wire.selectedApplicationIds,
                    );
                },

                selectAll() {
                    this.$wire.selectedApplicationIds =
                        this.$wire.applicationIds;
                },

                deselectAllOnPage() {
                    const idsOnPage = new Set(this.$wire.applicationIdsOnPage);
                    this.$wire.selectedApplicationIds =
                        this.$wire.selectedApplicationIds.filter(
                            (id) => !idsOnPage.has(id),
                        );
                },

                deselectAll() {
                    this.$wire.selectedApplicationIds = [];
                },
            };
        });
    </script>
@endscript

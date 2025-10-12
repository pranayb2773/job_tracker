<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('dashboard') }}">
            {{ __('Home') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('Documents') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Documents') }}</flux:heading>
        <flux:button icon:trailing="document-plus" size="sm" variant="primary">
            Create Document
        </flux:button>
    </div>

    <div x-data="checkAll">
        @include('livewire.document.filters')
        @include('livewire.document.table')
    </div>
</div>

@script
    <script>
        Alpine.data('checkAll', () => {
            return {
                init() {
                    this.$wire.$watch('selectedDocumentIds', () => {
                        this.updateCheckAllState();
                    });

                    this.$wire.$watch('documentIdsOnPage', () => {
                        this.updateCheckAllState();
                    });

                    this.$wire.$watch('documentIds', (newUsers) => {
                        if (!newUsers.length) {
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
                    return this.$wire.documentIdsOnPage.every((id) =>
                        this.$wire.selectedDocumentIds.includes(id),
                    );
                },

                pageIsEmpty() {
                    return this.$wire.selectedDocumentIds.length === 0;
                },

                handleCheck(e) {
                    e.target.hasAttribute('data-checked')
                        ? this.selectAllOnPage()
                        : this.deselectAllOnPage();
                },

                selectAllOnPage() {
                    this.$wire.documentIdsOnPage.forEach((id) => {
                        if (this.$wire.selectedDocumentIds.includes(id)) return;

                        this.$wire.selectedDocumentIds.push(id);
                    });
                },

                selectAll() {
                    this.$wire.selectedDocumentIds = this.$wire.documentIds;
                },

                deselectAllOnPage() {
                    const idsOnPage = new Set(this.$wire.documentIdsOnPage);
                    this.$wire.selectedDocumentIds =
                        this.$wire.selectedDocumentIds.filter(
                            (id) => !idsOnPage.has(id),
                        );
                },

                deselectAll() {
                    this.$wire.selectedDocumentIds = [];
                },
            };
        });
    </script>
@endscript

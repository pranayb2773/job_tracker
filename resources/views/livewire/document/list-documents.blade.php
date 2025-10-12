<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('dashboard') }}">
            {{ __('Home') }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ __('Documents') }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="flex items-center justify-between">
        <flux:heading size="xl">{{ __('Documents') }}</flux:heading>
        <flux:modal.trigger name="upload-document">
            <flux:button
                icon:trailing="cloud-arrow-up"
                size="sm"
                variant="primary"
            >
                Upload Document
            </flux:button>
        </flux:modal.trigger>
    </div>

    <div x-data="checkAll">
        @include('livewire.document.filters')
        @include('livewire.document.table')
    </div>

    {{-- Upload Document Modal --}}
    <flux:modal name="upload-document" class="md:w-[32rem]">
        <form wire:submit="uploadDocument">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Upload Document</flux:heading>
                    <flux:subheading>
                        Upload a new document to your library
                    </flux:subheading>
                </div>

                <flux:field>
                    <flux:label>File</flux:label>
                    <flux:file-upload wire:model="file" accept=".pdf">
                        <flux:file-upload.dropzone
                            heading="Drop files here or click to browse"
                            text="PDF up to 1MB"
                        />
                    </flux:file-upload>
                    <flux:description>
                        Accepted format: PDF only (Max: 1MB)
                    </flux:description>
                    <flux:error name="file" />
                </flux:field>

                <flux:field>
                    <flux:label>Title</flux:label>
                    <flux:input
                        wire:model="title"
                        placeholder="Auto-filled from filename"
                    />
                    <flux:description>
                        Title is automatically populated from the PDF filename
                    </flux:description>
                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label>Document Type</flux:label>
                    <flux:select
                        wire:model="type"
                        variant="listbox"
                        placeholder="Choose document type..."
                    >
                        @foreach (\App\Enums\DocumentType::cases() as $documentType)
                            <flux:select.option
                                value="{{ $documentType->value }}"
                            >
                                {{ $documentType->getLabel() }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="type" />
                </flux:field>

                <div class="flex gap-2">
                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>

                    <flux:button type="submit" variant="primary">
                        Upload Document
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
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

                    this.$wire.$watch('documentIds', (newDocuments) => {
                        if (!newDocuments.length) {
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

                    console.log('documentIds', this.$wire.documentIds);
                    console.log(
                        'selectedDocumentIds',
                        this.$wire.selectedDocumentIds,
                    );
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

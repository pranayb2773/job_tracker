<?php

declare(strict_types=1);

namespace App\Livewire\Application;

use App\Enums\ApplicationPriority;
use App\Enums\ApplicationStatus;
use App\Livewire\Forms\JobApplicationForm;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class CreateJobApplication extends Component
{
    public JobApplicationForm $form;

    public bool $showDocumentModal = false;

    public bool $showFastTrackModal = false;

    public array $selectedDocumentIds = [];

    public function mount(): void
    {
        $this->form->setDefaults();
    }

    public function render(): View
    {
        return view('livewire.application.create-job-application')
            ->title(config('app.name') . ' | New Application');
    }

    public function save(): void
    {
        $application = $this->form->store();

        session()->flash('success', 'Job application created successfully!');

        $this->redirect(route('applications.list'), navigate: true);
    }

    public function cancel(): void
    {
        $this->redirect(route('applications.list'), navigate: true);
    }

    public function addTag(string $tag): void
    {
        if (!empty($tag) && !in_array($tag, $this->form->tags, true)) {
            $this->form->tags[] = $tag;
        }
    }

    public function removeTag(int $index): void
    {
        unset($this->form->tags[$index]);
        $this->form->tags = array_values($this->form->tags);
    }

    public function openDocumentModal(): void
    {
        $this->showDocumentModal = true;
    }

    public function closeDocumentModal(): void
    {
        $this->showDocumentModal = false;
    }

    public function openFastTrackModal(): void
    {
        $this->showFastTrackModal = true;
    }

    public function closeFastTrackModal(): void
    {
        $this->showFastTrackModal = false;
    }

    public function attachDocument(int $documentId): void
    {
        if (!in_array($documentId, $this->form->document_ids, true)) {
            $this->form->document_ids[] = $documentId;
        }

        $this->closeDocumentModal();
    }

    public function detachDocument(int $documentId): void
    {
        $this->form->document_ids = array_values(
            array_filter($this->form->document_ids, fn(int $id) => $id !== $documentId)
        );
    }

    #[Computed]
    public function availableDocuments()
    {
        return Document::query()
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    #[Computed]
    public function attachedDocuments()
    {
        if (empty($this->form->document_ids)) {
            return collect([]);
        }

        return Document::query()
            ->whereIn('id', $this->form->document_ids)
            ->get();
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\Application;

use App\Livewire\Forms\JobApplicationForm;
use App\Models\Document;
use App\Models\JobApplication;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class EditJobApplication extends Component
{
    public JobApplicationForm $form;

    public JobApplication $application;

    public function mount(JobApplication $application): void
    {
        // Authorization check - ensure user owns this application
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $this->application = $application->load('documents');
        $this->form->setApplication($this->application);
    }

    public function render(): View
    {
        return view('livewire.application.edit-job-application')
            ->title(config('app.name') . ' | Edit Application');
    }

    public function save(): void
    {
        $this->form->update($this->application);

        Flux::toast(
            text: 'Application updated successfully.',
            heading: 'Application Updated',
            variant: 'success',
        );

        $this->redirect(route('applications.list'), navigate: true);
    }

    public function cancel(): void
    {
        $this->redirect(route('applications.list'), navigate: true);
    }

    #[Computed]
    public function availableDocuments(): Collection
    {
        return Document::query()
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

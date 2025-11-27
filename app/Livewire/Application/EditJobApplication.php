<?php

declare(strict_types=1);

namespace App\Livewire\Application;

use App\Enums\ApplicationStatus;
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
            ->title(config('app.name').' | Edit Application');
    }

    public function save(): void
    {
        $this->form->update($this->application);

        Flux::toast(
            text: 'Application updated successfully.',
            heading: 'Application Updated',
            variant: 'success',
        );

        $this->redirect(route('applications.show', $this->application), navigate: true);
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

    public function updatedFormStatus(string $value): void
    {
        $today = now()->format('Y-m-d');

        match ($value) {
            ApplicationStatus::Screening->value => $this->form->screening_date ??= $today,
            ApplicationStatus::Interview->value => $this->form->interview_date ??= $today,
            ApplicationStatus::TechnicalTest->value => $this->form->technical_test_date ??= $today,
            ApplicationStatus::FinalInterview->value => $this->form->final_interview_date ??= $today,
            ApplicationStatus::Offer->value => $this->form->offer_date ??= $today,
            ApplicationStatus::Accepted->value => $this->form->accepted_date ??= $today,
            ApplicationStatus::Rejected->value => $this->form->rejected_date ??= $today,
            ApplicationStatus::Withdrawn->value => $this->form->withdrawn_date ??= $today,
            default => null,
        };
    }
}

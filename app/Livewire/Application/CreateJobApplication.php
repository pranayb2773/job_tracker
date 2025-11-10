<?php

declare(strict_types=1);

namespace App\Livewire\Application;

use App\Livewire\Forms\JobApplicationForm;
use App\Models\Document;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class CreateJobApplication extends Component
{
    public JobApplicationForm $form;

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

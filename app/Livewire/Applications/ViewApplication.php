<?php

declare(strict_types=1);

namespace App\Livewire\Applications;

use App\Models\JobApplication;
use App\Models\Document;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ViewApplication extends Component
{
    public JobApplication $application;

    public function mount(JobApplication $application): void
    {
        // Ensure the user owns the application
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $this->application = $application->load('documents');
    }

    public function render(): View
    {
        return view('livewire.applications.view-application')
            ->title(config('app.name') . ' | ' . $this->application->job_title);
    }

    public function downloadDocument(int $documentId): ?StreamedResponse
    {
        $document = $this->application
            ->whereBelongsTo(Auth::user())
            ->documents()
            ->where('documents.id', $documentId)
            ->firstOrFail();

        if (!Storage::disk('local')->exists($document->file_path)) {
            Flux::toast(
                text: 'File not found.',
                heading: 'Download Failed',
                variant: 'danger',
            );

            return null;
        }

        return Storage::disk('local')->download(
            $document->file_path,
            $document->file_name
        );
    }
}

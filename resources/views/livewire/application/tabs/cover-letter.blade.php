<?php

use App\Enums\DocumentType;
use App\Services\AI\ApplicationAIService;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\RateLimiting\AnalysisRateLimiter;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

new class extends Component {
    public App\Models\JobApplication $application;
    public ?string $coverLetter = null;

    public function mount(App\Models\JobApplication $application): void
    {
        $this->application = $application->loadMissing('documents', 'coverLetter');
        $coverLetterData = $this->application->coverLetter?->data;
        $this->coverLetter = is_array($coverLetterData)
            ? (mb_trim((string)($coverLetterData['content'] ?? '')) ?: null)
            : null;
    }

    public function generateCoverLetter(ApplicationAIService $applicationAIService): void
    {
        $descHtml = (string)($this->application->job_description ?? '');
        $desc = mb_trim(strip_tags($descHtml));
        if (mb_strlen($desc) < 60) {
            Flux::toast(text: 'Please add a more complete job description to generate a cover letter.', heading: 'Job Description Required', variant: 'warning');
            return;
        }

        try {
            $result = $applicationAIService->generateCoverLetter($this->application);

            // Persist to AIAnalysis model using polymorphic relationship
            $this->application->coverLetter()->create([
                'data' => $result->data,
                'type' => 'cover_letter',
                'provider' => $result->provider,
                'model' => $result->model,
                'prompt_tokens' => $result->promptTokens,
                'completion_tokens' => $result->completionTokens,
                'analyzed_at' => now(),
            ]);

            $this->coverLetter = $result->data['content'];

            Flux::toast(text: 'Cover letter generated successfully.', heading: 'Done', variant: 'success');
        } catch (\Throwable $e) {
            Flux::toast(text: $e->getMessage(), heading: 'Generation Failed', variant: 'danger');

            logger()->error('Cover letter generation failed', ['user_id' => Auth::id(), 'application_id' => $this->application->id, 'error' => $e->getMessage(),]);
        }
    }

    public function downloadCoverLetter(): ?StreamedResponse
    {
        $coverLetterData = $this->application->coverLetter?->data;
        $content = (string)(is_array($coverLetterData) ? ($coverLetterData['content'] ?? $this->coverLetter ?? '') : ($this->coverLetter ?? ''));
        if ($content === '') {
            Flux::toast(text: 'No cover letter to download yet.', heading: 'Nothing to Download', variant: 'warning');
            return null;
        }

        $fileName = 'cover-letter-' . now()->format('Y-m-d-His') . '.txt';

        return response()->streamDownload(function () use ($content): void {
            echo $content;
        }, $fileName, ['Content-Type' => 'text/plain; charset=UTF-8',]);
    }
} ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">
            {{ __('Cover Letter Inspiration') }}
        </flux:heading>
        <div
            class="flex items-center gap-2"
            x-data="{ cover: @entangle('coverLetter') }"
        >
            @if ($coverLetter)
                <flux:button
                    variant="outline"
                    size="sm"
                    icon="clipboard"
                    @click="(function(text){ if(!text) return; if (window.navigator && window.navigator.clipboard && window.navigator.clipboard.writeText){ window.navigator.clipboard.writeText(text).then(()=>{ $flux.toast({ heading: 'Copied', text: 'Cover letter copied to clipboard.', variant: 'success' }); }).catch(()=>{ const ta=document.createElement('textarea'); ta.value=text; ta.setAttribute('readonly',''); ta.style.position='absolute'; ta.style.left='-9999px'; document.body.appendChild(ta); ta.select(); try{ document.execCommand('copy'); $flux.toast({ heading: 'Copied', text: 'Cover letter copied to clipboard.', variant: 'success' }); } catch(e){ $flux.toast({ heading: 'Copy failed', text: 'Please copy manually.', variant: 'danger' }); } document.body.removeChild(ta); }); } else { const ta=document.createElement('textarea'); ta.value=text; ta.setAttribute('readonly',''); ta.style.position='absolute'; ta.style.left='-9999px'; document.body.appendChild(ta); ta.select(); try{ document.execCommand('copy'); $flux.toast({ heading: 'Copied', text: 'Cover letter copied to clipboard.', variant: 'success' }); } catch(e){ $flux.toast({ heading: 'Copy failed', text: 'Please copy manually.', variant: 'danger' }); } document.body.removeChild(ta); } })(cover)"
                    x-bind:disabled="! (cover && cover.length)"
                >
                    {{ __('Copy') }}
                </flux:button>
                <flux:button
                    variant="primary"
                    size="sm"
                    icon="arrow-down-tray"
                    wire:click="downloadCoverLetter"
                >
                    {{ __('Download') }}
                </flux:button>
            @endif
            <flux:button
                variant="primary"
                icon="sparkles"
                wire:click="generateCoverLetter"
                size="sm"
            >
                @if ($coverLetter)
                    {{ __('Regenerate') }}
                @else
                    {{ __('Generate') }}
                @endif
            </flux:button>
        </div>
    </div>

    @if (! $coverLetter)
        <div
            class="space-y-6 rounded-lg bg-zinc-100 px-6 text-sm text-center dark:bg-zinc-900 py-16"
        >
            <p class="text-zinc-600 dark:text-zinc-400">
                {{ __('Add a job description and cv to generate the cover letter.') }}
            </p>
        </div>
    @endif

    @if ($coverLetter)
        <flux:card>
            <div
                class="text-sm text-zinc-600 dark:text-zinc-400 mb-2"
            >
                {{ $application->job_title }} @
                {{ $application->organisation }}
            </div>
            <div
                class="not-prose text-sm text-zinc-800 dark:text-zinc-200 leading-relaxed whitespace-pre-line"
            >
                {{ $coverLetter }}
            </div>
        </flux:card>
    @endif
</div>

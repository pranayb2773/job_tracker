<?php

use App\Enums\DocumentType;
use App\Jobs\ProcessCoverLetter;
use App\Services\AI\Contracts\AIProviderInterface;
use App\Services\AI\RateLimiting\AnalysisRateLimiter;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    public App\Models\JobApplication $application;
    public ?string $coverLetter = null;
    public bool $isGenerating = false;

    public function mount(App\Models\JobApplication $application): void
    {
        $this->application = $application->loadMissing('documents');
        $this->coverLetter = is_array($this->application->cover_letter ?? null)
            ? (mb_trim((string)($this->application->cover_letter['content'] ?? '')) ?: null)
            : null;
    }

    public function generateCoverLetter(): void
    {
        $descHtml = (string)($this->application->job_description ?? '');
        $desc = mb_trim(strip_tags($descHtml));
        if (mb_strlen($desc) < 60) {
            Flux::toast(text: 'Please add a more complete job description to generate a cover letter.', heading: 'Job Description Required', variant: 'warning');
            return;
        }

        $limiter = app(AnalysisRateLimiter::class);
        try {
            $limiter->check(Auth::user(), 'role_analysis');
        } catch (\Throwable $e) {
            Flux::toast(text: 'Daily limit reached for AI generation. Try again tomorrow.', heading: 'Limit Reached', variant: 'warning');
            return;
        }

        $systemPrompt = mb_trim((string)view('prompts.cover-letter')->render()) ?: 'Draft a concise, UK English cover letter using the provided job description and CV attachment. Keep under 350 words.';

        $cv = $this->application->documents->first(fn($d) => $d->type?->value === DocumentType::CurriculumVitae->value);

        $input = "JOB DESCRIPTION:\n" . $desc;
        if ($cv) {
            $input .= "\n\nCV FILENAME:\n" . ($cv->file_name ?? 'cv.pdf') . "\n";
        }
        $input .= "\n\nROLE: " . ($this->application->job_title ?? '');
        $input .= "\nORG: " . ($this->application->organisation ?? '');

        $this->isGenerating = true;
        $this->coverLetter = null; // Clear old data to prevent false success message

        // Dispatch job to process in background
        ProcessCoverLetter::dispatch($this->application, $input, $systemPrompt);

        $limiter->hit(Auth::user(), 'role_analysis');

        Flux::toast(
            text: 'Cover letter generation started. This may take 2-3 minutes. The page will update automatically when complete.',
            heading: 'Processing...',
            variant: 'info'
        );
    }

    #[On('refresh-cover-letter')]
    public function refreshCoverLetter(): void
    {
        // Refresh the application model from database
        $this->application = $this->application->fresh();
        $newData = is_array($this->application->cover_letter ?? null)
            ? (mb_trim((string)($this->application->cover_letter['content'] ?? '')) ?: null)
            : null;

        // Only show success if we actually got NEW data (wasn't null before)
        if ($newData && !$this->coverLetter && $this->isGenerating) {
            $this->isGenerating = false;
            $this->coverLetter = $newData;
            Flux::toast(text: 'Cover letter generated successfully.', heading: 'Done', variant: 'success');
        } else {
            $this->coverLetter = $newData;
        }
    }

    public function downloadCoverLetter(): ?\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $content = (string)($this->application->cover_letter['content'] ?? $this->coverLetter ?? '');
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

<div class="space-y-6" @if($isGenerating) wire:poll.5s="refreshCoverLetter" @endif>
    @if ($coverLetter && $application->documents->isNotEmpty())
        <div class="flex items-center justify-between">
            <flux:heading size="lg">
                {{ __('Cover Letter Inspiration') }}
            </flux:heading>
            <div
                class="flex items-center gap-2"
                x-data="{ cover: @entangle('coverLetter') }"
            >
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
    @else
        <flux:heading size="lg">
            {{ __('Cover Letter Inspiration') }}
        </flux:heading>
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


<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Enums\ApplicationPriority;
use App\Enums\ApplicationStatus;
use App\Enums\JobType;
use App\Models\Document;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Form;

final class JobApplicationForm extends Form
{
    public string $job_title = '';

    public string $organisation = '';

    public ?string $location = null;

    public ?string $type = null;

    public ?string $job_description = null;

    public ?string $job_url = null;

    public ?string $source = null;

    public ?string $source_url = null;

    public ?string $work_arrangement = null;

    public ?string $salary_range = null;

    public ?int $salary_min = null;

    public ?string $application_date = null;

    public ?string $deadline = null;

    public ?string $status = null;

    public ?string $screening_date = null;

    public ?string $interview_date = null;

    public ?string $technical_test_date = null;

    public ?string $final_interview_date = null;

    public ?string $offer_date = null;

    public ?string $accepted_date = null;

    public ?string $rejected_date = null;

    public ?string $withdrawn_date = null;

    public ?string $priority = null;

    public array $tags = [];

    public array $document_ids = [];

    public ?string $notes = null;

    public function rules(): array
    {
        return [
            'job_title' => ['required', 'string', 'max:255'],
            'organisation' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', Rule::enum(JobType::class)],
            'job_description' => ['nullable', 'string'],
            'job_url' => ['nullable', 'url', 'max:500'],
            'source' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'url', 'max:500'],
            'work_arrangement' => ['nullable', 'string', 'max:255'],
            'salary_range' => ['nullable', 'string', 'max:255'],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'application_date' => ['required', 'date'],
            'screening_date' => ['nullable', 'date'],
            'interview_date' => ['nullable', 'date'],
            'technical_test_date' => ['nullable', 'date'],
            'final_interview_date' => ['nullable', 'date'],
            'offer_date' => ['nullable', 'date'],
            'accepted_date' => ['nullable', 'date'],
            'rejected_date' => ['nullable', 'date'],
            'withdrawn_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date', 'after_or_equal:application_date'],
            'status' => ['required', Rule::enum(ApplicationStatus::class)],
            'priority' => ['nullable', Rule::enum(ApplicationPriority::class)],
            'tags' => ['nullable', 'array'],
            'document_ids' => ['nullable', 'array'],
            'document_ids.*' => ['exists:documents,id'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'job_title.required' => 'The job title field is required.',
            'organisation.required' => 'The organisation field is required.',
            'application_date.required' => 'The application date is required.',
            'deadline.after_or_equal' => 'The deadline must be on or after the application date.',
            'status.required' => 'Please select a status.',
        ];
    }

    public function store(): JobApplication
    {
        $this->validate();

        return DB::transaction(function () {
            $application = JobApplication::query()->create([
                'user_id' => Auth::id(),
                'job_title' => $this->job_title,
                'organisation' => $this->organisation,
                'location' => $this->location,
                'type' => $this->type,
                'job_description' => $this->job_description,
                'job_url' => $this->job_url,
                'source' => $this->source,
                'source_url' => $this->source_url,
                'work_arrangement' => $this->work_arrangement,
                'salary_range' => $this->salary_range,
                'salary_min' => $this->salary_min,
                'application_date' => $this->application_date,
                'screening_date' => $this->screening_date,
                'interview_date' => $this->interview_date,
                'technical_test_date' => $this->technical_test_date,
                'final_interview_date' => $this->final_interview_date,
                'offer_date' => $this->offer_date,
                'accepted_date' => $this->accepted_date,
                'rejected_date' => $this->rejected_date,
                'withdrawn_date' => $this->withdrawn_date,
                'deadline' => $this->deadline,
                'status' => $this->status ?? ApplicationStatus::Applied->value,
                'priority' => $this->priority ?? ApplicationPriority::Medium->value,
                'tags' => $this->tags,
                'notes' => $this->notes,
            ]);

            // Attach documents if any
            if (! empty($this->document_ids)) {
                $application->documents()->attach($this->document_ids);
            }

            return $application;
        });
    }

    public function setDefaults(): void
    {
        $this->application_date = now()->format('Y-m-d');
        $this->status = ApplicationStatus::Applied->value;
        $this->priority = ApplicationPriority::Medium->value;
    }

    public function setApplication(JobApplication $application): void
    {
        $this->job_title = $application->job_title;
        $this->organisation = $application->organisation;
        $this->location = $application->location;
        $this->type = $application->type?->value;
        $this->job_description = $application->job_description;
        $this->job_url = $application->job_url;
        $this->source = $application->source;
        $this->source_url = $application->source_url;
        $this->work_arrangement = $application->work_arrangement;
        $this->salary_range = $application->salary_range;
        $this->salary_min = $application->salary_min;
        $this->application_date = $application->application_date?->format('Y-m-d');
        $this->screening_date = $application->screening_date?->format('Y-m-d');
        $this->interview_date = $application->interview_date?->format('Y-m-d');
        $this->technical_test_date = $application->technical_test_date?->format('Y-m-d');
        $this->final_interview_date = $application->final_interview_date?->format('Y-m-d');
        $this->offer_date = $application->offer_date?->format('Y-m-d');
        $this->accepted_date = $application->accepted_date?->format('Y-m-d');
        $this->rejected_date = $application->rejected_date?->format('Y-m-d');
        $this->withdrawn_date = $application->withdrawn_date?->format('Y-m-d');
        $this->deadline = $application->deadline?->format('Y-m-d');
        $this->status = $application->status->value;
        $this->priority = $application->priority->value;
        $this->tags = $application->tags ?? [];
        $this->document_ids = $application->documents->pluck('id')->toArray();
        $this->notes = $application->notes;
    }

    public function update(JobApplication $application): JobApplication
    {
        $this->validate();

        return DB::transaction(function () use ($application) {
            // Preserve previously saved stage dates unless explicitly changed
            $preserve = fn($prop, $attr) => $prop !== null ? $prop : $application->$attr?->format('Y-m-d');

            $application->update([
                'job_title' => $this->job_title,
                'organisation' => $this->organisation,
                'location' => $this->location,
                'type' => $this->type,
                'job_description' => $this->job_description,
                'job_url' => $this->job_url,
                'source' => $this->source,
                'source_url' => $this->source_url,
                'work_arrangement' => $this->work_arrangement,
                'salary_range' => $this->salary_range,
                'salary_min' => $this->salary_min,
                'application_date' => $this->application_date,
                'screening_date' => $preserve($this->screening_date, 'screening_date'),
                'interview_date' => $preserve($this->interview_date, 'interview_date'),
                'technical_test_date' => $preserve($this->technical_test_date, 'technical_test_date'),
                'final_interview_date' => $preserve($this->final_interview_date, 'final_interview_date'),
                'offer_date' => $preserve($this->offer_date, 'offer_date'),
                'accepted_date' => $preserve($this->accepted_date, 'accepted_date'),
                'rejected_date' => $preserve($this->rejected_date, 'rejected_date'),
                'withdrawn_date' => $preserve($this->withdrawn_date, 'withdrawn_date'),
                'deadline' => $this->deadline,
                'status' => $this->status,
                'priority' => $this->priority,
                'tags' => $this->tags,
                'notes' => $this->notes,
            ]);

            // Sync documents
            $application->documents()->sync($this->document_ids);

            return $application->fresh();
        });
    }
}

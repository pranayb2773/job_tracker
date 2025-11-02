<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Enums\ApplicationPriority;
use App\Enums\ApplicationStatus;
use App\Models\Document;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

final class JobApplicationForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $job_title = '';

    #[Validate('required|string|max:255')]
    public string $organisation = '';

    #[Validate('nullable|string')]
    public ?string $job_description = null;

    #[Validate('nullable|url|max:500')]
    public ?string $job_url = null;

    #[Validate('nullable|string|max:255')]
    public ?string $source = null;

    #[Validate('nullable|url|max:500')]
    public ?string $source_url = null;

    #[Validate('nullable|string|max:255')]
    public ?string $work_arrangement = null;

    #[Validate('nullable|string|max:255')]
    public ?string $salary_range = null;

    #[Validate('nullable|integer|min:0')]
    public ?int $salary_min = null;

    #[Validate('nullable|date')]
    public ?string $application_date = null;

    #[Validate('nullable|date')]
    public ?string $deadline = null;

    #[Validate('nullable')]
    public ?string $status = null;

    #[Validate('nullable')]
    public ?string $priority = null;

    #[Validate('nullable|array')]
    public array $tags = [];

    #[Validate('nullable|array')]
    public array $document_ids = [];

    #[Validate('nullable|string')]
    public ?string $notes = null;

    public function rules(): array
    {
        return [
            'job_title' => ['required', 'string', 'max:255'],
            'organisation' => ['required', 'string', 'max:255'],
            'job_description' => ['nullable', 'string'],
            'job_url' => ['nullable', 'url', 'max:500'],
            'source' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'url', 'max:500'],
            'work_arrangement' => ['nullable', 'string', 'max:255'],
            'salary_range' => ['nullable', 'string', 'max:255'],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'application_date' => ['required', 'date'],
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

        $application = JobApplication::query()->create([
            'user_id' => Auth::id(),
            'job_title' => $this->job_title,
            'organisation' => $this->organisation,
            'job_description' => $this->job_description,
            'job_url' => $this->job_url,
            'source' => $this->source,
            'source_url' => $this->source_url,
            'work_arrangement' => $this->work_arrangement,
            'salary_range' => $this->salary_range,
            'salary_min' => $this->salary_min,
            'application_date' => $this->application_date,
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
    }

    public function setDefaults(): void
    {
        $this->application_date = now()->format('Y-m-d');
        $this->status = ApplicationStatus::Applied->value;
        $this->priority = ApplicationPriority::Medium->value;
    }
}

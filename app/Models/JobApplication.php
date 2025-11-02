<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ApplicationPriority;
use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class JobApplication extends Model
{
    protected $fillable = [
        'user_id',
        'job_title',
        'job_description',
        'job_url',
        'organisation',
        'source',
        'source_url',
        'work_arrangement',
        'salary_range',
        'salary_min',
        'status',
        'priority',
        'application_date',
        'screening_date',
        'interview_date',
        'technical_test_date',
        'final_interview_date',
        'follow_up_date',
        'offer_date',
        'accepted_date',
        'rejected_date',
        'withdrawn_date',
        'deadline',
        'notes',
        'tags',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'job_application_documents')
            ->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'application_date' => 'date',
            'screening_date' => 'date',
            'interview_date' => 'date',
            'technical_test_date' => 'date',
            'final_interview_date' => 'date',
            'offer_date' => 'date',
            'accepted_date' => 'date',
            'rejected_date' => 'date',
            'withdrawn_date' => 'date',
            'deadline' => 'date',
            'follow_up_date' => 'date',
            'tags' => 'array',
            'status' => ApplicationStatus::class,
            'priority' => ApplicationPriority::class,
        ];
    }
}

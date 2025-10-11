<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class JobApplication extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(JobApplicationDocument::class, 'job_application_documents')
            ->withTimestamps();
    }

    protected function casts()
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

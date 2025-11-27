<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ApplicationPriority;
use App\Enums\ApplicationStatus;
use App\Enums\JobType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

final class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_title',
        'job_description',
        'job_url',
        'organisation',
        'location',
        'type',
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

    /**
     * Get all AI analyses for this job application.
     */
    public function aiAnalyses(): MorphMany
    {
        return $this->morphMany(AIAnalysis::class, 'analyzable');
    }

    /**
     * Get the role analysis.
     */
    public function roleAnalysis(): MorphOne
    {
        return $this->morphOne(AIAnalysis::class, 'analyzable')
            ->where('type', 'role_analysis')->latest();
    }

    /**
     * Get the profile matching analysis.
     */
    public function profileMatching(): MorphOne
    {
        return $this->morphOne(AIAnalysis::class, 'analyzable')
            ->where('type', 'profile_matching')->latest();
    }

    /**
     * Get the cover letter.
     */
    public function coverLetter(): MorphOne
    {
        return $this->morphOne(AIAnalysis::class, 'analyzable')
            ->where('type', 'cover_letter')->latest();
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
            'type' => JobType::class,
        ];
    }
}

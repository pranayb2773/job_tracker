<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

final class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'type',
        'file_name',
        'file_path',
        'file_mime_type',
        'file_size',
        'file_hash',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobApplications(): BelongsToMany
    {
        return $this->belongsToMany(JobApplication::class, 'job_application_documents')
            ->withTimestamps();
    }

    /**
     * Get all AI analyses for this document.
     */
    public function aiAnalyses(): MorphMany
    {
        return $this->morphMany(AIAnalysis::class, 'analyzable')
            ->where('type', 'document');
    }

    /**
     * Get the document analysis.
     */
    public function lastestAnalysis(): MorphOne
    {
        return $this->morphOne(AIAnalysis::class, 'analyzable')
            ->where('type', 'document')->latest();
    }

    protected function fileSizeFormatted(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $bytes = $this->file_size;

                if ($bytes >= 1048576) {
                    return number_format($bytes / 1048576, 2).' MB';
                }
                if ($bytes >= 1024) {
                    return number_format($bytes / 1024, 2).' KB';
                }

                return $bytes.' B';
            }
        );
    }

    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
            'file_size' => 'integer',
        ];
    }
}

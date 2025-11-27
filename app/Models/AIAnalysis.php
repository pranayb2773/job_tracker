<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class AIAnalysis extends Model
{
    protected $table = 'ai_analyses';

    protected $fillable = [
        'analyzable_id',
        'analyzable_type',
        'type',
        'data',
        'provider',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'analyzed_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'analyzed_at' => 'datetime',
            'prompt_tokens' => 'integer',
            'completion_tokens' => 'integer',
        ];
    }

    /**
     * Get the parent-analyzable model (Document or JobApplication).
     */
    public function analyzable(): MorphTo
    {
        return $this->morphTo();
    }
}

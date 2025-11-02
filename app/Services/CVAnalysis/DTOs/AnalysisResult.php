<?php

declare(strict_types=1);

namespace App\Services\CVAnalysis\DTOs;

final readonly class AnalysisResult
{
    public function __construct(
        public array $data,
        public int $promptTokens,
        public int $completionTokens,
        public int $cacheWriteTokens,
        public int $cacheReadTokens,
        public bool $cacheHit,
        public string $provider,
        public string $model,
    ) {}

    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'usage' => [
                'prompt_tokens' => $this->promptTokens,
                'completion_tokens' => $this->completionTokens,
                'cache_write_tokens' => $this->cacheWriteTokens,
                'cache_read_tokens' => $this->cacheReadTokens,
                'cache_hit' => $this->cacheHit,
            ],
            'provider' => $this->provider,
            'model' => $this->model,
        ];
    }
}

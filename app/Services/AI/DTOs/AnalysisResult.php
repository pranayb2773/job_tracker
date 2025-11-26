<?php

declare(strict_types=1);

namespace App\Services\AI\DTOs;

final readonly class AnalysisResult
{
    public function __construct(
        public array  $data,
        public int    $promptTokens,
        public int    $completionTokens,
        public string $provider,
        public string $model,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'usage' => [
                'prompt_tokens' => $this->promptTokens,
                'completion_tokens' => $this->completionTokens,
            ],
            'provider' => $this->provider,
            'model' => $this->model,
        ];
    }
}

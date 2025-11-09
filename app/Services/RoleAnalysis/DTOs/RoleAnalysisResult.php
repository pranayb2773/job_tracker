<?php

declare(strict_types=1);

namespace App\Services\RoleAnalysis\DTOs;

final readonly class RoleAnalysisResult
{
    public function __construct(
        public array $data,
        public string $provider,
        public string $model,
    ) {}

    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'provider' => $this->provider,
            'model' => $this->model,
            'analyzed_at' => now()->toIso8601String(),
        ];
    }
}

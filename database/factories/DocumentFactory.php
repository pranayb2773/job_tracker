<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DocumentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
final class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->words(3, true),
            'type' => fake()->randomElement(DocumentType::cases())->value,
            'file_name' => fake()->word().'.pdf',
            'file_path' => 'documents/user-1/'.fake()->uuid().'.pdf',
            'file_mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(100000, 1000000),
            'file_hash' => hash('sha256', fake()->uuid()),
        ];
    }

    /**
     * Indicate that the document is a CV.
     */
    public function cv(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => DocumentType::CurriculumVitae->value,
            'title' => fake()->name().' - CV',
        ]);
    }

    /**
     * Indicate that the document has been analyzed.
     */
    public function analyzed(): static
    {
        return $this->afterCreating(function (\App\Models\Document $document) {
            $document->aiAnalyses()->create([
                'type' => 'document',
                'data' => [
                    'overall_score' => fake()->numberBetween(60, 100),
                    'summary' => fake()->paragraph(),
                    'score_label' => fake()->randomElement(['GOOD', 'STRONG', 'EXCELLENT']),
                    'score_description' => fake()->sentence(),
                    'top_recommendations' => [
                        fake()->sentence(),
                        fake()->sentence(),
                        fake()->sentence(),
                    ],
                    'scoring_dimensions' => [
                        'metadata_contact' => [
                            'score' => fake()->numberBetween(70, 100),
                            'label' => 'Metadata & Contact Information',
                            'description' => fake()->sentence(),
                        ],
                    ],
                    'penalties' => [],
                    'section_analysis' => [
                        'professional_summary' => [
                            'status' => 'success',
                            'feedback' => fake()->sentence(),
                        ],
                    ],
                ],
                'provider' => fake()->randomElement(['gemini', 'claude', 'groq']),
                'model' => 'test-model',
                'prompt_tokens' => fake()->numberBetween(100, 500),
                'completion_tokens' => fake()->numberBetween(50, 200),
                'analyzed_at' => now(),
            ]);
        });
    }
}

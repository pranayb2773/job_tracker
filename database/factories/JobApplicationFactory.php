<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ApplicationPriority;
use App\Enums\ApplicationStatus;
use App\Enums\JobType;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobApplication>
 */
final class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'job_title' => fake()->jobTitle(),
            'job_description' => fake()->paragraphs(3, true),
            'job_url' => fake()->url(),
            'organisation' => fake()->company(),
            'location' => fake()->city(),
            'type' => fake()->randomElement(JobType::cases())->value,
            'source' => 'other',
            'status' => ApplicationStatus::Applied->value,
            'priority' => ApplicationPriority::Medium->value,
            'application_date' => now(),
        ];
    }
}

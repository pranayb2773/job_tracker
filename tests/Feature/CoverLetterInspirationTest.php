<?php

declare(strict_types=1);

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('shows generate button when no cover letter exists', function (): void {
    /** @var User $user */
    $user = User::factory()->create();
    actingAs($user);

    /** @var JobApplication $application */
    $application = JobApplication::factory()->create([
        'user_id' => $user->id,
        'status' => ApplicationStatus::Applied->value,
        'cover_letter' => null,
    ]);

    $this->get(route('applications.show', $application))
        ->assertOk()
        ->assertSee('Cover Letter Inspiration')
        ->assertSee('Generate')
        ->assertDontSee('Regenerate');
});

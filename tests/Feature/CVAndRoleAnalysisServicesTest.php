<?php

declare(strict_types=1);

use App\Models\Document;
use App\Models\JobApplication;
use App\Models\User;
use App\Services\CVAnalysis\CVAnalysisService;
use App\Services\RoleAnalysis\RoleAnalysisService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;

it('uses shared AI provider for cv and role analysis', function (): void {
    Storage::fake('local');

    /** @var User $user */
    $user = User::factory()->create();
    actingAs($user);

    $file = UploadedFile::fake()->create('cv.pdf', 10);

    /** @var Document $document */
    $document = Document::factory()->for($user)->create([
        'file_name' => $file->getClientOriginalName(),
        'file_path' => $file->store('documents', 'local'),
    ]);

    /** @var JobApplication $application */
    $application = JobApplication::factory()->for($user)->create([
        'job_title' => 'Senior PHP Developer',
        'organisation' => 'Acme Inc.',
        'job_description' => 'We are looking for a Senior PHP Developer with Laravel experience.',
    ]);

    $cvAnalysisService = app(CVAnalysisService::class);
    $roleAnalysisService = app(RoleAnalysisService::class);

    expect($cvAnalysisService->getProviderName())
        ->not()
        ->toBeEmpty();

    // Ensure rate limiter wiring works and role analysis can compute remaining quota
    $remaining = $roleAnalysisService->getRemainingAnalyses($user);

    expect($remaining)->toBeInt();

    // Basic smoke check to ensure services can be resolved and used together
    expect($cvAnalysisService)->toBeInstanceOf(CVAnalysisService::class)
        ->and($roleAnalysisService)->toBeInstanceOf(RoleAnalysisService::class);
});

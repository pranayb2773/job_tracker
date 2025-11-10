<?php

declare(strict_types=1);

use App\Livewire\AI\RoleAnalysis;
use App\Livewire\Application\CreateJobApplication;
use App\Livewire\Application\EditJobApplication;
use App\Livewire\Application\ListJobApplications;
use App\Livewire\Document\AnalyzeDocument;
use App\Livewire\Document\ListDocuments;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Route::get('applications', ListJobApplications::class)->name('applications.list');
    Route::get('applications/create', CreateJobApplication::class)->name('applications.create');
    Route::get('applications/{application}/edit', EditJobApplication::class)->name('applications.edit');
    Route::get('documents', ListDocuments::class)->name('documents.list');
    Route::get('documents/{document:file_hash}/analyze', AnalyzeDocument::class)->name('documents.analyze');
    Route::get('role-analysis', RoleAnalysis::class)->name('role-analysis.show');
});

require __DIR__ . '/auth.php';

# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **Job Application Tracker** built with Laravel 12, Livewire 3, Volt, and Flux Pro UI. The application helps users track their job applications throughout the entire hiring process, from initial application to final offer or rejection.

## Key Stack & Versions

- **PHP**: 8.3.25 with strict types (`declare(strict_types=1)`)
- **Laravel**: 12.x (modern streamlined structure)
- **Livewire**: 3.x with **Livewire Volt** for single-file components
- **Flux Pro**: 2.x (premium UI component library)
- **Testing**: Pest 4.x with browser testing support
- **Frontend**: Vite + Tailwind CSS 4.x
- **Authentication**: Laravel Fortify with 2FA support

## Development Commands

### Running the Application
```bash
# Full development environment (server + queue + logs + vite)
composer run dev

# Individual services
php artisan serve           # Application server
npm run dev                 # Vite dev server
npm run build              # Production build
```

### Testing
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run with filter
php artisan test --filter=testName

# Browser tests (Pest 4)
php artisan test tests/Browser/
```

### Code Quality
```bash
# Format PHP code (Laravel Pint - REQUIRED before commits)
vendor/bin/pint --dirty

# Format Blade templates (Prettier)
npx prettier --write "resources/**/*.blade.php"
```

### Database
```bash
# Run migrations
php artisan migrate

# Fresh database with seeders
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_something_table --no-interaction
```

## Architecture & Patterns

### Core Domain Models

The application centers around **job applications** that users track through various stages:

- **JobApplication**: Main entity tracking a job opportunity
  - Status workflow: Applied → Screening → Interview → Technical Test → Final Interview → Offer → Accepted/Rejected/Withdrawn
  - Each status has corresponding date fields (application_date, interview_date, etc.)
  - Supports priority levels (via ApplicationPriority enum)
  - Tags stored as JSON array
  - Belongs to User, has many Documents

- **Document**: Files attached to applications (resume, cover letter, portfolio, etc.)
  - Type tracked via DocumentType enum
  - Can be linked to multiple JobApplications
  - Stores file metadata (name, path, size, mime_type)

- **Enums**: All enums use backed string enums with helper methods:
  - `ApplicationStatus`: Includes `getLabel()`, `getOrder()`, `getColor()` for UI rendering
  - `ApplicationPriority`: Priority levels for applications
  - `DocumentType`: Types of documents users can attach

### Laravel 12 Structure

This project uses Laravel 12's streamlined structure:

- **No `app/Console/Kernel.php`** - Commands auto-register from `app/Console/Commands/`
- **No `app/Http/Kernel.php`** - Middleware registered in `bootstrap/app.php`
- **No middleware directory** - Custom middleware defined inline in bootstrap
- **Service Providers**: Listed in `bootstrap/providers.php`
- Models use `casts()` method instead of `$casts` property

### Livewire Volt

All interactive pages use **Livewire Volt** for single-file components:

- Volt files are in `resources/views/livewire/` with `.blade.php` extension
- Use class-based syntax: `new class extends Component {}`
- PHP logic at top, Blade template below
- Tests use `Volt::test('component-name')` syntax

### Flux Pro UI Components

Use Flux Pro components extensively for consistent UI:

**Available Components**: accordion, autocomplete, avatar, badge, brand, breadcrumbs, button, calendar, callout, card, chart, checkbox, command, context, date-picker, dropdown, editor, field, heading, icon, input, modal, navbar, pagination, popover, profile, radio, select, separator, switch, table, tabs, text, textarea, toast, tooltip

Example usage:
```blade
<flux:button variant="primary" wire:click="save">
    Save Application
</flux:button>

<flux:input wire:model.live="search" placeholder="Search jobs..." />

<flux:table>
    <flux:table.header>
        <!-- ... -->
    </flux:table.header>
</flux:table>
```

### Code Style Enforcement

**Strict PHP Standards** (enforced by Pint):
- `declare(strict_types=1)` on all PHP files
- All classes are `final` unless meant for extension
- Explicit return types on all methods
- Constructor property promotion: `public function __construct(public User $user) {}`
- Strict comparisons (`===`, `!==`)
- Global namespace imports for classes, constants, and functions

**Blade/Frontend**:
- Prettier with blade plugin (4 space indentation, single quotes)
- Tailwind 4.x utility classes (no deprecated v3 utilities)
- Dark mode support throughout (use `dark:` classes)

## Testing Strategy

### Test Organization
- Feature tests: `tests/Feature/` (most tests should be feature tests)
- Unit tests: `tests/Unit/`
- Browser tests: `tests/Browser/` (Pest 4 browser testing)
- Volt component tests: Test Volt components using `Volt::test()`

### Testing Conventions
- All tests use Pest syntax
- Use model factories for test data
- Use specific assertions: `assertForbidden()`, `assertNotFound()` (not `assertStatus(403)`)
- Leverage Pest datasets for validation testing
- Browser tests can use Laravel features (factories, `Event::fake()`, etc.)

Example Volt test:
```php
test('job application form creates application', function () {
    $user = User::factory()->create();

    Volt::test('pages.applications.create')
        ->actingAs($user)
        ->set('form.job_title', 'Software Engineer')
        ->set('form.organisation', 'Acme Corp')
        ->call('save')
        ->assertHasNoErrors();

    expect(JobApplication::where('job_title', 'Software Engineer')->exists())->toBeTrue();
});
```

## Important Conventions

### Models & Relationships
- Always use explicit Eloquent relationship methods with return type hints
- Avoid raw queries and `DB::` facade - prefer `Model::query()`
- Use eager loading to prevent N+1 queries
- Define casts in `casts()` method, not `$casts` property

### Validation
- Create Form Request classes for all validation (not inline in controllers)
- Form Requests should include both rules and custom error messages

### Configuration
- Never use `env()` outside config files
- Always use `config('app.key')` in application code

### Authentication & Authorization
- Uses Laravel Fortify for authentication (including 2FA)
- User settings pages: profile, password, appearance, two-factor, delete account
- Define policies for authorization checks

## Key Files & Directories

- `resources/views/livewire/` - Volt components (auth, settings, application pages)
- `app/Models/` - Eloquent models (User, JobApplication, Document)
- `app/Enums/` - Backed enums with helper methods
- `app/Livewire/Actions/` - Livewire action classes
- `database/migrations/` - Database schema
- `bootstrap/app.php` - Middleware, routing, exception handling
- `routes/web.php` - Web routes
- `routes/auth.php` - Authentication routes

## Laravel Boost Integration

This project uses **Laravel Boost MCP** for enhanced tooling:
- `search-docs` - Search version-specific Laravel ecosystem docs
- `tinker` - Execute PHP/Eloquent queries for debugging
- `database-query` - Read-only database queries
- `browser-logs` - View browser console logs and errors
- `list-artisan-commands` - List available Artisan commands with options

Always use these tools when available instead of manual alternatives.

## Workflow Notes

1. **Before making changes**: Check sibling files for conventions
2. **When creating models**: Generate with factory and seeder (`php artisan make:model -mfs`)
3. **After code changes**: Run `vendor/bin/pint --dirty` to format
4. **After tests pass**: Offer to run full test suite
5. **Frontend changes not showing**: User may need to run `npm run dev` or `composer run dev`

## Project Status

Currently implemented:
- Authentication system with 2FA
- User settings and profile management
- Core models and migrations for job tracking
- Dark mode support throughout

The application is in active development for building out the job application tracking features.

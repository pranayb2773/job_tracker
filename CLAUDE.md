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

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.14
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v12
- laravel/nightwatch (NIGHTWATCH) - v1
- laravel/prompts (PROMPTS) - v0
- livewire/flux (FLUXUI_FREE) - v2
- livewire/flux-pro (FLUXUI_PRO) - v2
- livewire/livewire (LIVEWIRE) - v3
- livewire/volt (VOLT) - v1
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- prettier (PRETTIER) - v3
- tailwindcss (TAILWINDCSS) - v4

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== herd rules ===

## Laravel Herd

- The application is served by Laravel Herd and will be available at: https?://[kebab-case-project-dir].test. Use the `get-absolute-url` tool to generate URLs for the user to ensure valid URLs.
- You must not run any commands to make the site available via HTTP(s). It is _always_ available through Laravel Herd.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] <name>` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.


=== fluxui-pro/core rules ===

## Flux UI Pro

- This project is using the Pro version of Flux UI. It has full access to the free components and variants, as well as full access to the Pro components and variants.
- Flux UI is a component library for Livewire. Flux is a robust, hand-crafted, UI component library for your Livewire applications. It's built using Tailwind CSS and provides a set of components that are easy to use and customize.
- You should use Flux UI components when available.
- Fallback to standard Blade components if Flux is unavailable.
- If available, use Laravel Boost's `search-docs` tool to get the exact documentation and code snippets available for this project.
- Flux UI components look like this:

<code-snippet name="Flux UI component usage example" lang="blade">
    <flux:button variant="primary"/>
</code-snippet>


### Available Components
This is correct as of Boost installation, but there may be additional components within the codebase.

<available-flux-components>
accordion, autocomplete, avatar, badge, brand, breadcrumbs, button, calendar, callout, card, chart, checkbox, command, context, date-picker, dropdown, editor, field, heading, file upload, icon, input, modal, navbar, pagination, pillbox, popover, profile, radio, select, separator, switch, table, tabs, text, textarea, toast, tooltip
</available-flux-components>


=== livewire/core rules ===

## Livewire Core
- Use the `search-docs` tool to find exact version specific documentation for how to write Livewire & Livewire tests.
- Use the `php artisan make:livewire [Posts\CreatePost]` artisan command to create new components
- State should live on the server, with the UI reflecting it.
- All Livewire requests hit the Laravel backend, they're like regular HTTP requests. Always validate form data, and run authorization checks in Livewire actions.

## Livewire Best Practices
- Livewire components require a single root element.
- Use `wire:loading` and `wire:dirty` for delightful loading states.
- Add `wire:key` in loops:

    ```blade
    @foreach ($items as $item)
        <div wire:key="item-{{ $item->id }}">
            {{ $item->name }}
        </div>
    @endforeach
    ```

- Prefer lifecycle hooks like `mount()`, `updatedFoo()` for initialization and reactive side effects:

<code-snippet name="Lifecycle hook examples" lang="php">
    public function mount(User $user) { $this->user = $user; }
    public function updatedSearch() { $this->resetPage(); }
</code-snippet>


## Testing Livewire

<code-snippet name="Example Livewire component test" lang="php">
    Livewire::test(Counter::class)
        ->assertSet('count', 0)
        ->call('increment')
        ->assertSet('count', 1)
        ->assertSee(1)
        ->assertStatus(200);
</code-snippet>


    <code-snippet name="Testing a Livewire component exists within a page" lang="php">
        $this->get('/posts/create')
        ->assertSeeLivewire(CreatePost::class);
    </code-snippet>


=== livewire/v3 rules ===

## Livewire 3

### Key Changes From Livewire 2
- These things changed in Livewire 2, but may not have been updated in this application. Verify this application's setup to ensure you conform with application conventions.
    - Use `wire:model.live` for real-time updates, `wire:model` is now deferred by default.
    - Components now use the `App\Livewire` namespace (not `App\Http\Livewire`).
    - Use `$this->dispatch()` to dispatch events (not `emit` or `dispatchBrowserEvent`).
    - Use the `components.layouts.app` view as the typical layout path (not `layouts.app`).

### New Directives
- `wire:show`, `wire:transition`, `wire:cloak`, `wire:offline`, `wire:target` are available for use. Use the documentation to find usage examples.

### Alpine
- Alpine is now included with Livewire, don't manually include Alpine.js.
- Plugins included with Alpine: persist, intersect, collapse, and focus.

### Lifecycle Hooks
- You can listen for `livewire:init` to hook into Livewire initialization, and `fail.status === 419` for the page expiring:

<code-snippet name="livewire:load example" lang="js">
document.addEventListener('livewire:init', function () {
    Livewire.hook('request', ({ fail }) => {
        if (fail && fail.status === 419) {
            alert('Your session expired');
        }
    });

    Livewire.hook('message.failed', (message, component) => {
        console.error(message);
    });
});
</code-snippet>


=== volt/core rules ===

## Livewire Volt

- This project uses Livewire Volt for interactivity within its pages. New pages requiring interactivity must also use Livewire Volt. There is documentation available for it.
- Make new Volt components using `php artisan make:volt [name] [--test] [--pest]`
- Volt is a **class-based** and **functional** API for Livewire that supports single-file components, allowing a component's PHP logic and Blade templates to co-exist in the same file
- Livewire Volt allows PHP logic and Blade templates in one file. Components use the `@livewire("volt-anonymous-fragment-eyJuYW1lIjoidm9sdC1hbm9ueW1vdXMtZnJhZ21lbnQtYmQ5YWJiNTE3YWMyMTgwOTA1ZmUxMzAxODk0MGJiZmIiLCJwYXRoIjoic3RvcmFnZVxcZnJhbWV3b3JrXFx2aWV3c1wvMTUxYWRjZWRjMzBhMzllOWIxNzQ0ZDRiMWRjY2FjYWIuYmxhZGUucGhwIn0=", Livewire\Volt\Precompilers\ExtractFragments::componentArguments([...get_defined_vars(), ...array (
)]))
</code-snippet>


### Volt Class Based Component Example
To get started, define an anonymous class that extends Livewire\Volt\Component. Within the class, you may utilize all of the features of Livewire using traditional Livewire syntax:


<code-snippet name="Volt Class-based Volt Component Example" lang="php">
use Livewire\Volt\Component;

new class extends Component {
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }
} ?>

<div>
    <h1>{{ $count }}</h1>
    <button wire:click="increment">+</button>
</div>
</code-snippet>


### Testing Volt & Volt Components
- Use the existing directory for tests if it already exists. Otherwise, fallback to `tests/Feature/Volt`.

<code-snippet name="Livewire Test Example" lang="php">
use Livewire\Volt\Volt;

test('counter increments', function () {
    Volt::test('counter')
        ->assertSee('Count: 0')
        ->call('increment')
        ->assertSee('Count: 1');
});
</code-snippet>


<code-snippet name="Volt Component Test Using Pest" lang="php">
declare(strict_types=1);

use App\Models\{User, Product};
use Livewire\Volt\Volt;

test('product form creates product', function () {
    $user = User::factory()->create();

    Volt::test('pages.products.create')
        ->actingAs($user)
        ->set('form.name', 'Test Product')
        ->set('form.description', 'Test Description')
        ->set('form.price', 99.99)
        ->call('create')
        ->assertHasNoErrors();

    expect(Product::where('name', 'Test Product')->exists())->toBeTrue();
});
</code-snippet>


### Common Patterns


<code-snippet name="CRUD With Volt" lang="php">
<?php

use App\Models\Product;
use function Livewire\Volt\{state, computed};

state(['editing' => null, 'search' => '']);

$products = computed(fn() => Product::when($this->search,
    fn($q) => $q->where('name', 'like', "%{$this->search}%")
)->get());

$edit = fn(Product $product) => $this->editing = $product->id;
$delete = fn(Product $product) => $product->delete();

?>

<!-- HTML / UI Here -->
</code-snippet>

<code-snippet name="Real-Time Search With Volt" lang="php">
    <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="Search..."
    />
</code-snippet>

<code-snippet name="Loading States With Volt" lang="php">
    <flux:button wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove>Save</span>
        <span wire:loading>Saving...</span>
    </flux:button>
</code-snippet>


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== pest/core rules ===

## Pest

### Testing
- If you need to verify a feature is working, write or update a Unit / Feature test.

### Pest Tests
- All tests must be written using Pest. Use `php artisan make:test --pest <name>`.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files - these are core to the application.
- Tests should test all of the happy paths, failure paths, and weird paths.
- Tests live in the `tests/Feature` and `tests/Unit` directories.
- Pest tests look and behave like this:
<code-snippet name="Basic Pest Test Example" lang="php">
it('is true', function () {
    expect(true)->toBeTrue();
});
</code-snippet>

### Running Tests
- Run the minimal number of tests using an appropriate filter before finalizing code edits.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).
- When the tests relating to your changes are passing, ask the user if they would like to run the entire test suite to ensure everything is still passing.

### Pest Assertions
- When asserting status codes on a response, use the specific method like `assertForbidden` and `assertNotFound` instead of using `assertStatus(403)` or similar, e.g.:
<code-snippet name="Pest Example Asserting postJson Response" lang="php">
it('returns all', function () {
    $response = $this->postJson('/api/docs', []);

    $response->assertSuccessful();
});
</code-snippet>

### Mocking
- Mocking can be very helpful when appropriate.
- When mocking, you can use the `Pest\Laravel\mock` Pest function, but always import it via `use function Pest\Laravel\mock;` before using it. Alternatively, you can use `$this->mock()` if existing tests do.
- You can also create partial mocks using the same import or self method.

### Datasets
- Use datasets in Pest to simplify tests which have a lot of duplicated data. This is often the case when testing validation rules, so consider going with this solution when writing tests for validation rules.

<code-snippet name="Pest Dataset Example" lang="php">
it('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with([
    'james' => 'james@laravel.com',
    'taylor' => 'taylor@laravel.com',
]);
</code-snippet>


=== pest/v4 rules ===

## Pest 4

- Pest v4 is a huge upgrade to Pest and offers: browser testing, smoke testing, visual regression testing, test sharding, and faster type coverage.
- Browser testing is incredibly powerful and useful for this project.
- Browser tests should live in `tests/Browser/`.
- Use the `search-docs` tool for detailed guidance on utilizing these features.

### Browser Testing
- You can use Laravel features like `Event::fake()`, `assertAuthenticated()`, and model factories within Pest v4 browser tests, as well as `RefreshDatabase` (when needed) to ensure a clean state for each test.
- Interact with the page (click, type, scroll, select, submit, drag-and-drop, touch gestures, etc.) when appropriate to complete the test.
- If requested, test on multiple browsers (Chrome, Firefox, Safari).
- If requested, test on different devices and viewports (like iPhone 14 Pro, tablets, or custom breakpoints).
- Switch color schemes (light/dark mode) when appropriate.
- Take screenshots or pause tests for debugging when appropriate.

### Example Tests

<code-snippet name="Pest Browser Test Example" lang="php">
it('may reset the password', function () {
    Notification::fake();

    $this->actingAs(User::factory()->create());

    $page = visit('/sign-in'); // Visit on a real browser...

    $page->assertSee('Sign In')
        ->assertNoJavascriptErrors() // or ->assertNoConsoleLogs()
        ->click('Forgot Password?')
        ->fill('email', 'nuno@laravel.com')
        ->click('Send Reset Link')
        ->assertSee('We have emailed your password reset link!')

    Notification::assertSent(ResetPassword::class);
});
</code-snippet>

<code-snippet name="Pest Smoke Testing Example" lang="php">
$pages = visit(['/', '/about', '/contact']);

$pages->assertNoJavascriptErrors()->assertNoConsoleLogs();
</code-snippet>


=== tailwindcss/core rules ===

## Tailwind Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc..)
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing, don't use margins.

    <code-snippet name="Valid Flex Gap Spacing Example" lang="html">
        <div class="flex gap-8">
            <div>Superior</div>
            <div>Michigan</div>
            <div>Erie</div>
        </div>
    </code-snippet>


### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.


=== tailwindcss/v4 rules ===

## Tailwind 4

- Always use Tailwind CSS v4 - do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff">
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>


### Replaced Utilities
- Tailwind v4 removed deprecated utilities. Do not use the deprecated option - use the replacement.
- Opacity values are still numeric.

| Deprecated |	Replacement |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |


=== tests rules ===

## Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test` with a specific filename or filter.


=== laravel/fortify rules ===

## Laravel Fortify

Fortify is a headless authentication backend that provides authentication routes and controllers for Laravel applications.

**Before implementing any authentication features, use the `search-docs` tool to get the latest docs for that specific feature.**

### Configuration & Setup
- Check `config/fortify.php` to see what's enabled. Use `search-docs` for detailed information on specific features.
- Enable features by adding them to the `'features' => []` array: `Features::registration()`, `Features::resetPasswords()`, etc.
- To see the all Fortify registered routes, use the `list-routes` tool with the `only_vendor: true` and `action: "Fortify"` parameters.
- Fortify includes view routes by default (login, register). Set `'views' => false` in the configuration file to disable them if you're handling views yourself.

### Customization
- Views can be customized in `FortifyServiceProvider`'s `boot()` method using `Fortify::loginView()`, `Fortify::registerView()`, etc.
- Customize authentication logic with `Fortify::authenticateUsing()` for custom user retrieval / validation.
- Actions in `app/Actions/Fortify/` handle business logic (user creation, password reset, etc.). They're fully customizable, so you can modify them to change feature behavior.

## Available Features
- `Features::registration()` for user registration.
- `Features::emailVerification()` to verify new user emails.
- `Features::twoFactorAuthentication()` for 2FA with QR codes and recovery codes.
  - Add options: `['confirmPassword' => true, 'confirm' => true]` to require password confirmation and OTP confirmation before enabling 2FA.
- `Features::updateProfileInformation()` to let users update their profile.
- `Features::updatePasswords()` to let users change their passwords.
- `Features::resetPasswords()` for password reset via email.


=== prism-php/prism rules ===

## Prism

### Package Overview
- Prism is a powerful Laravel package for integrating Large Language Models (LLMs) into applications with a fluent, expressive API.
- Prism supports multiple AI providers: OpenAI, Anthropic, Ollama, Mistral, Groq, XAI, Gemini, VoyageAI, ElevenLabs, DeepSeek, and OpenRouter.
- Always use the `Prism` facade, class, or `prism()` helper function for all LLM interactions.
- Prism draws inspiration from the Vercel AI SDK, adapting its concepts for the Laravel ecosystem.

### Basic Usage Patterns
- Use `Prism::text()` for text generation, `Prism::structured()` for structured output, `Prism::embeddings()` for embeddings, `Prism::image()` for image generation, and `Prism::audio()` for audio processing.
- Always chain the `using()` method to specify provider and model before generating responses.
- Use `asText()`, `asStructured()`, `asStream()`, `asEmbeddings()`, etc. to finalize the request based on the desired response type.
- You can also use the fluent `prism()` helper function as an alternative to the Prism facade.

<code-snippet name="Basic Text Generation" lang="php">
use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;

$response = Prism::text()
    ->using(Provider::OpenAI, 'gpt-4')
    ->withPrompt('Explain quantum computing to a 5-year-old.')
    ->asText();

echo $response->text;

// Or using the helper function
$response = prism()
    ->text()
    ->using(Provider::OpenAI, 'gpt-4')
    ->withPrompt('Explain quantum computing to a 5-year-old.')
    ->asText();
</code-snippet>

### Provider Configuration
- Provider configurations are stored in `config/prism.php` and typically use environment variables.
- The `Provider` enum provides type safety when specifying providers.
- Configuration can be overridden dynamically using the third parameter of `using()` or `usingProviderConfig()`.

<code-snippet name="Provider Usage with Configuration" lang="php">
// Basic provider usage
$response = Prism::text()
    ->using(Provider::OpenAI, 'gpt-4o')
    ->withPrompt('Generate a product description')
    ->asText();

// Override config inline
$response = Prism::text()
    ->using(Provider::OpenAI, 'gpt-4o', ['url' => 'custom-endpoint'])
    ->withPrompt('Generate content')
    ->asText();

// Or using usingProviderConfig()
$response = Prism::text()
    ->using(Provider::OpenAI, 'gpt-4o')
    ->usingProviderConfig(['url' => 'custom-endpoint'])
    ->withPrompt('Generate content')
    ->asText();
</code-snippet>

### Structured Output
- Use `Prism::structured()` when you need predictable, typed responses from LLMs.
- Define schemas using Prism's schema classes: `ObjectSchema`, `StringSchema`, `NumberSchema`, `ArraySchema`, `BooleanSchema`, etc.
- **IMPORTANT**: For OpenAI structured output (especially strict mode), the root schema MUST be an `ObjectSchema`. Other schema types can only be used as properties within an ObjectSchema.
- Different providers support either structured mode (strict schema validation) or JSON mode (approximate schema matching).
- Access structured data via `$response->structured` which returns a PHP array.
- Consider validating structured responses based on your application's needs.

<code-snippet name="Structured Output with Schema" lang="php">
use Prism\Prism\Prism;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;
use Prism\Prism\Schema\NumberSchema;

// Root schema must be ObjectSchema for OpenAI
$schema = new ObjectSchema(
    name: 'product',
    description: 'Product information',
    properties: [
        new StringSchema('name', 'Product name'),
        new StringSchema('description', 'Product description'),
        new NumberSchema('price', 'Price in dollars'),
    ],
    requiredFields: ['name', 'description', 'price']
);

$response = Prism::structured()
    ->using(Provider::OpenAI, 'gpt-4o')
    ->withSchema($schema)
    ->withPrompt('Generate a product for a coffee shop')
    ->asStructured();

// Access structured data
$product = $response->structured;
if ($product !== null) {
    echo $product['name'];
}
</code-snippet>

### Streaming Responses
- Use `asStream()` for real-time response streaming, especially for long-form content generation.
- Always iterate through stream chunks and handle them appropriately for your application.
- Streaming works seamlessly with tools - you can detect tool calls and results in the stream.
- Consider using Laravel's event streaming capabilities for frontend integration.
- Be aware that Laravel Telescope may interfere with streaming - disable it if needed.

<code-snippet name="Streaming Text Generation" lang="php">
use Prism\Prism\Enums\ChunkType;

$stream = Prism::text()
    ->using(Provider::Anthropic, 'claude-3-5-sonnet-20241022')
    ->withPrompt('Write a detailed article about renewable energy')
    ->asStream();

foreach ($stream as $chunk) {
    echo $chunk->text;
    // Process each chunk as it arrives
    ob_flush();
    flush();
    
    // Check for final chunk
    if ($chunk->finishReason === FinishReason::Stop) {
        echo "Generation complete!";
    }
}

// Streaming with tools
$stream = Prism::text()
    ->using(Provider::OpenAI, 'gpt-4o')
    ->withTools([$weatherTool])
    ->withMaxSteps(3)
    ->withPrompt('What\'s the weather like in San Francisco?')
    ->asStream();

foreach ($stream as $chunk) {
    // Check chunk type for tool interactions
    if ($chunk->chunkType === ChunkType::ToolCall) {
        foreach ($chunk->toolCalls as $call) {
            echo "Tool called: " . $call->name;
        }
    } elseif ($chunk->chunkType === ChunkType::ToolResult) {
        foreach ($chunk->toolResults as $result) {
            echo "Tool result: " . $result->result;
        }
    } else {
        echo $chunk->text;
    }
}

// Laravel 12 Event Streams
Route::get('/chat', function () {
    return response()->eventStream(function () {
        $stream = Prism::text()
            ->using('openai', 'gpt-4')
            ->withPrompt('Explain quantum computing step by step.')
            ->asStream();

        foreach ($stream as $response) {
            yield $response->text;
        }
    });
});
</code-snippet>

### Multi-Modal Inputs
- Prism supports images, documents, audio, and video inputs alongside text prompts.
- Use appropriate value objects: `Image::fromLocalPath()`, `Document::fromLocalPath()`, `Audio::fromPath()`, etc.
- Images can be loaded from local paths, storage disks, URLs, or base64 data.
- Documents support PDF and other formats, with optional titles for better context.
- Provide descriptive text in your prompts along with media for better results.
- Check provider support tables as not all providers support all modalities.

<code-snippet name="Multi-Modal Input Example" lang="php">
use Prism\Prism\ValueObjects\Media\Image;
use Prism\Prism\ValueObjects\Media\Document;

// Image from local path
$response = Prism::text()
    ->using(Provider::OpenAI, 'gpt-4o')
    ->withPrompt(
        'Analyze this image and describe what you see',
        [Image::fromLocalPath('/path/to/image.jpg')]
    )
    ->asText();

// Image from storage disk
$response = Prism::text()
    ->using(Provider::Anthropic, 'claude-3-5-sonnet-20241022')
    ->withPrompt(
        'What is in this image?',
        [Image::fromStoragePath('images/photo.jpg', 'public')]
    )
    ->asText();

// Document analysis
$response = Prism::text()
    ->using(Provider::Anthropic, 'claude-3-5-sonnet-20241022')
    ->withPrompt(
        'Summarize this document',
        [Document::fromLocalPath('report.pdf', 'Quarterly Report')]
    )
    ->asText();
</code-snippet>

### Tools and Function Calling
- Use the `Tool` facade to define functions that LLMs can call during generation.
- Tools have names, descriptions, and parameters that the LLM can use.
- **IMPORTANT**: When using tools, set `withMaxSteps(2)` or higher to allow multi-step interactions.
- Prism defaults to a single step, but tools require at least 2 steps (tool call + response).
- Supports multiple parameter types: string, number, boolean, enum, array, and object parameters.

<code-snippet name="Tool Definition and Usage" lang="php">
use Prism\Prism\Facades\Tool;

$weatherTool = Tool::as('weather')
    ->for('Get current weather conditions')
    ->withStringParameter('city', 'The city to get weather for')
    ->using(function (string $city): string {
        // Your weather API logic here
        return "The weather in {$city} is sunny and 72°F.";
    });

$response = Prism::text()
    ->using(Provider::Anthropic, 'claude-3-5-sonnet-latest')
    ->withMaxSteps(2) // Required for tools!
    ->withPrompt('What is the weather like in Paris?')
    ->withTools([$weatherTool])
    ->asText();

// Complex tool with multiple parameter types
$calculatorTool = Tool::as('calculator')
    ->for('Perform mathematical calculations')
    ->withStringParameter('expression', 'Mathematical expression to calculate')
    ->withBooleanParameter('round_result', 'Whether to round the result', false)
    ->using(function (string $expression, bool $roundResult = false): string {
        $result = eval("return $expression;");
        return $roundResult ? (string) round($result) : (string) $result;
    });

// Custom tool class example
class WeatherTool extends Tool
{
    public function __construct()
    {
        $this
            ->as('weather')
            ->for('Get current weather information for a city')
            ->withStringParameter('city', 'The city name to get weather for')
            ->withStringParameter('units', 'Temperature units (celsius/fahrenheit)', false)
            ->using($this);
    }

    public function __invoke(string $city, string $units = 'celsius'): string
    {
        // Your weather API implementation
        $weatherData = $this->fetchWeatherData($city, $units);
        
        return "Weather in {$city}: {$weatherData['temperature']}°" . 
               ($units === 'celsius' ? 'C' : 'F') . 
               ", {$weatherData['condition']}";
    }

    private function fetchWeatherData(string $city, string $units): array
    {
        // Implementation would call actual weather API
        return [
            'temperature' => 22,
            'condition' => 'Sunny'
        ];
    }
}

// Usage
$weatherTool = new WeatherTool();
$response = Prism::text()
    ->using(Provider::OpenAI, 'gpt-4')
    ->withMaxSteps(2)
    ->withPrompt('What\'s the weather like in London?')
    ->withTools([$weatherTool])
    ->asText();
</code-snippet>

### System Prompts and Context
- Use `withSystemPrompt()` to set behavior, persona, or context for the LLM.
- System prompts help maintain consistent behavior across interactions.
- Laravel views can be used for both system prompts and regular prompts for dynamic content.
- For conversation history, use `withMessages()` with message objects instead of single prompts.

<code-snippet name="System Prompt Usage" lang="php">
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\SystemMessage;

$response = Prism::text()
    ->using(Provider::Anthropic, 'claude-3-5-sonnet-20241022')
    ->withSystemPrompt('You are a helpful assistant.')
    ->withPrompt('Review this code and suggest improvements')
    ->asText();

// Using Laravel views for dynamic prompts
$response = Prism::text()
    ->using(Provider::OpenAI, 'gpt-4')
    ->withSystemPrompt(view('prompts.code-review-assistant', ['language' => 'PHP']))
    ->withPrompt($codeToReview)
    ->asText();

// Views work with regular prompts too
$response = Prism::text()
    ->using(Provider::OpenAI, 'gpt-4')
    ->withPrompt(view('prompts.analysis-request', ['data' => $analysisData]))
    ->asText();

// For conversations, use messages
$response = Prism::text()
    ->using(Provider::Anthropic, 'claude-3-5-sonnet-20241022')
    ->withMessages([
        new UserMessage('What is JSON?'),
        new AssistantMessage('JSON is a lightweight data format...'),
        new UserMessage('Can you show me an example?')
    ])
    ->asText();
</code-snippet>

### Testing with Prism
- Use `Prism::fake()` in tests to avoid making real API calls.
- Use response fake builders like `TextResponseFake::make()` for fluent test setup.
- Provide expected responses that match your testing needs.
- Test both successful responses and error conditions.
- Use Prism's assertion methods to verify requests, prompts, and provider configurations.

<code-snippet name="Testing with Prism Fake" lang="php">
use Prism\Prism\Prism;
use Prism\Prism\Testing\TextResponseFake;
use Prism\Prism\ValueObjects\Usage;

it('generates text responses', function () {
    $fakeResponse = TextResponseFake::make()
        ->withText('Generated response text')
        ->withUsage(new Usage(50, 25));

    $fake = Prism::fake([$fakeResponse]);

    $response = Prism::text()
        ->using(Provider::OpenAI, 'gpt-4')
        ->withPrompt('Test prompt')
        ->asText();

    expect($response->text)->toBe('Generated response text');
    
    // Assert on the request
    $fake->assertPrompt('Test prompt');
    $fake->assertCallCount(1);
    $fake->assertRequest(function ($requests) {
        expect($requests[0]->model())->toBe('gpt-4');
    });
});

// Testing multiple responses (for tool usage)
it('handles tool calls', function () {
    $responses = [
        TextResponseFake::make()->withToolCalls([/* tool calls */]),
        TextResponseFake::make()->withText('Final response after tool execution'),
    ];

    $fake = Prism::fake($responses);
    // Test your multi-step interaction
});
</code-snippet>

### Response Handling and Finish Reasons
- Always check finish reasons to understand why generation stopped.
- Handle multi-step responses when using tools by examining each step.
- Access token usage statistics for monitoring and cost management.
- Use response messages to maintain conversation history.

<code-snippet name="Complete Response Handling" lang="php">
use Prism\Prism\Enums\FinishReason;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;

$response = Prism::text()
    ->using(Provider::Anthropic, 'claude-3-5-sonnet-20241022')
    ->withPrompt('Explain quantum computing.')
    ->asText();

// Check why generation stopped
switch ($response->finishReason) {
    case FinishReason::Stop:
        echo "Generation completed normally";
        break;
    case FinishReason::Length:
        echo "Generation stopped due to max tokens";
        break;
    case FinishReason::ContentFilter:
        echo "Content was filtered";
        break;
    case FinishReason::ToolCalls:
        echo "Generation stopped for tool calls";
        break;
}

// Access token usage
echo "Prompt tokens: {$response->usage->promptTokens}";
echo "Completion tokens: {$response->usage->completionTokens}";

// For multi-step generations (with tools)
foreach ($response->steps as $step) {
    echo "Step text: {$step->text}";
    echo "Step tokens: {$step->usage->completionTokens}";
    
    // Check for tool calls in this step
    if ($step->toolCalls) {
        foreach ($step->toolCalls as $call) {
            echo "Called tool: {$call->name}";
        }
    }
}

// Access conversation history
foreach ($response->responseMessages as $message) {
    if ($message instanceof AssistantMessage) {
        echo "Assistant said: {$message->content}";
    }
}
</code-snippet>

### Error Handling
- Consider wrapping Prism calls in try-catch blocks based on your application's error handling strategy.
- Handle specific Prism exceptions appropriately: rate limits, API errors, validation failures.
- Implement fallback behavior when LLM calls fail as needed.

<code-snippet name="Error Handling" lang="php">
use Prism\Prism\Exceptions\PrismException;

try {
    $response = Prism::text()
        ->using(Provider::OpenAI, 'gpt-4')
        ->withPrompt('Generate content')
        ->asText();
        
    return $response->text;
} catch (PrismException $e) {
    // Handle Prism-specific errors
    return 'Content generation temporarily unavailable';
}
</code-snippet>

### Audio Processing
- Use `Prism::audio()` for both text-to-speech (TTS) and speech-to-text (STT) functionality.
- For TTS, specify voice and audio format options through provider-specific settings.
- For STT, provide audio files using the `Audio` value object from local paths or storage.
- Audio responses provide base64-encoded data that you can save to files or stream directly.

<code-snippet name="Audio Processing Examples" lang="php">
use Prism\Prism\ValueObjects\Media\Audio;

// Text-to-Speech
$response = Prism::audio()
    ->using(Provider::OpenAI, 'tts-1')
    ->withInput('Hello, this is a test of text-to-speech functionality.')
    ->withVoice('alloy')
    ->asAudio();

if ($response->audio->hasBase64()) {
    file_put_contents('output.mp3', base64_decode($response->audio->base64));
}

// Speech-to-Text
$audioFile = Audio::fromPath('/path/to/audio.mp3');

$response = Prism::audio()
    ->using(Provider::OpenAI, 'whisper-1')
    ->withInput($audioFile)
    ->asText();

echo $response->text; // Transcribed text
</code-snippet>

### Embeddings
- Use `Prism::embeddings()` to generate vector representations of text for semantic search and recommendations.
- Generate single or multiple embeddings in one request (except Gemini which only supports single embeddings).
- Access embeddings via `$response->embeddings[0]->embedding` which returns a float array.
- Use embeddings for similarity calculations, clustering, and semantic search implementations.

<code-snippet name="Embeddings Generation" lang="php">
// Single embedding
$response = Prism::embeddings()
    ->using(Provider::OpenAI, 'text-embedding-3-large')
    ->fromInput('Your text to embed')
    ->asEmbeddings();

$embedding = $response->embeddings[0]->embedding; // float[]
echo "Token usage: " . $response->usage->tokens;

// Multiple embeddings at once
$response = Prism::embeddings()
    ->using(Provider::OpenAI, 'text-embedding-3-large')
    ->fromInput('First text')
    ->fromInput('Second text')
    ->fromArray(['Third text', 'Fourth text'])
    ->asEmbeddings();

foreach ($response->embeddings as $embedding) {
    // Process each embedding vector
    $vector = $embedding->embedding;
}
</code-snippet>

### Performance and Best Practices
- Choose appropriate models for your use case based on speed, cost, and capability requirements.
- Consider token usage and costs when designing prompts.
- Use streaming for long-running generations to improve user experience.
- Be aware that Laravel Telescope and similar packages may interfere with streaming.
- Cache responses when appropriate to avoid redundant API calls.

### Provider-Specific Features
- Take advantage of provider-specific capabilities like OpenAI's reasoning models, Anthropic's thinking modes, and prompt caching.
- Use `withProviderOptions()` to access provider-specific parameters.
- Check provider documentation for unique features and limitations.
- Some providers support reasoning/thinking tokens that show the model's thought process.

<code-snippet name="Provider-Specific Options" lang="php">
use Prism\Prism\ValueObjects\Messages\SystemMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;

// OpenAI reasoning models with different effort levels
$response = Prism::text()
    ->using(Provider::OpenAI, 'gpt-5-mini')
    ->withPrompt('Solve this complex problem step by step')
    ->withProviderOptions([
        'reasoning' => ['effort' => 'high'] // 'low', 'medium', 'high'
    ])
    ->asText();

// Access reasoning token usage
$usage = $response->firstStep()->usage;
echo "Reasoning tokens: " . $usage->thoughtTokens;

// Anthropic extended thinking mode
$response = Prism::text()
    ->using(Provider::Anthropic, 'claude-3-7-sonnet-latest')
    ->withPrompt('Think through this complex problem carefully')
    ->withProviderOptions([
        'thinking' => ['enabled' => true, 'budget' => 2048]
    ])
    ->asText();

// Anthropic prompt caching (must use withMessages, not withPrompt)
$response = Prism::text()
    ->using(Provider::Anthropic, 'claude-3-5-sonnet-20241022')
    ->withMessages([
        (new SystemMessage('Long reusable system message...'))
            ->withProviderOptions(['cacheType' => 'ephemeral']),
        (new UserMessage('Long reusable user message...'))
            ->withProviderOptions(['cacheType' => 'ephemeral'])
    ])
    ->asText();

// XAI thinking mode with streaming
$stream = Prism::text()
    ->using(Provider::XAI, 'grok-4')
    ->withPrompt('Complex reasoning task')
    ->withProviderOptions(['thinking' => true])
    ->asStream();

foreach ($stream as $chunk) {
    if ($chunk->chunkType === ChunkType::Thinking) {
        echo "Thinking: " . $chunk->text;
    } else {
        echo "Answer: " . $chunk->text;
    }
}
</code-snippet>

### Prism Server
- Prism Server provides HTTP API access to your Prism functionality.
- Use the `PrismServer` facade to register named Prism configurations for HTTP access.
- Configure security with middleware and authentication as needed.
- Prism Server is disabled by default - enable via `PRISM_SERVER_ENABLED=true` environment variable.

<code-snippet name="Prism Server Configuration" lang="php">
// Register Prism configurations
use Prism\Prism\Facades\PrismServer;

PrismServer::register('chat-assistant', function () {
    return Prism::text()
        ->using(Provider::OpenAI, 'gpt-4')
        ->withSystemPrompt('You are a helpful assistant.');
});

// In config/prism.php
'prism_server' => [
    'enabled' => env('PRISM_SERVER_ENABLED', false),
    'middleware' => [], // Configure as needed
],
</code-snippet>

### Image Generation
- Use `Prism::image()` for AI-powered image generation with supported providers.
- Configure image parameters like size, quality, and style through provider options.
- Images are returned as base64-encoded data or URLs depending on the provider.

<code-snippet name="Image Generation" lang="php">
// Basic image generation
$response = Prism::image()
    ->using(Provider::OpenAI, 'dall-e-3')
    ->withPrompt('A serene mountain landscape at sunset')
    ->generate();

if ($response->hasImages()) {
    $image = $response->firstImage();
    if ($image->hasUrl()) {
        echo "Image URL: " . $image->url;
    }
    if ($image->hasBase64()) {
        file_put_contents('generated.png', base64_decode($image->base64));
    }
}

// With provider-specific options
$response = Prism::image()
    ->using(Provider::OpenAI, 'dall-e-3')
    ->withPrompt('Abstract art in vibrant colors')
    ->withProviderOptions([
        'size' => '1024x1024',
        'quality' => 'hd',
        'style' => 'vivid'
    ])
    ->generate();
</code-snippet>

### Provider Tools
- Some providers offer built-in tools like code execution, web search, and file analysis.
- Use `withProviderTools()` with `ProviderTool` objects to enable these capabilities.
- Provider tools can be combined with custom tools for powerful interactions.
- Each provider offers different built-in capabilities - check documentation for availability.

<code-snippet name="Provider Tools Usage" lang="php">
use Prism\Prism\ValueObjects\ProviderTool;
use Prism\Prism\Facades\Tool;

// Using Anthropic's code execution tool
$response = Prism::text()
    ->using(Provider::Anthropic, 'claude-3-5-sonnet-latest')
    ->withPrompt('Calculate the fibonacci sequence up to 100 and plot it')
    ->withProviderTools([
        new ProviderTool(
            type: 'code_execution_20250522',
            name: 'code_execution'
        )
    ])
    ->asText();

// Combining provider tools with custom tools
$customTool = Tool::as('database_lookup')
    ->for('Look up user information')
    ->withStringParameter('user_id', 'The user ID to look up')
    ->using(function (string $userId): string {
        return User::find($userId)->toJson();
    });

$response = Prism::text()
    ->using(Provider::Anthropic, 'claude-3-5-sonnet-latest')
    ->withMaxSteps(5)
    ->withPrompt('Look up user 123 and analyze their usage statistics')
    ->withTools([$customTool])
    ->withProviderTools([
        new ProviderTool(type: 'code_execution_20250522', name: 'code_execution')
    ])
    ->asText();
</code-snippet>

### Advanced Configuration Options
- Use `withClientOptions()` to configure HTTP client settings like timeouts and retries.
- Use `withMaxTokens()`, `usingTemperature()`, and `usingTopP()` to fine-tune generation parameters.
- Override provider configuration dynamically with `usingProviderConfig()` for multi-tenant apps.

<code-snippet name="Advanced Configuration" lang="php">
$response = Prism::text()
    ->using(Provider::OpenAI, 'gpt-4')
    ->withPrompt('Generate detailed analysis')
    ->withMaxTokens(2000)
    ->usingTemperature(0.7)
    ->withClientOptions([
        'timeout' => 60,
        'connect_timeout' => 10
    ])
    ->withClientRetry(3, 1000)
    ->usingProviderConfig([
        'api_key' => $userApiKey, // Multi-tenant API key
        'organization' => $userOrgId
    ])
    ->asText();
</code-snippet>

### Integration Options
- Prism integrates with Laravel features: queues, events, broadcasting, caching.
- Use Laravel's queue system for long-running AI tasks to avoid timeouts.
- The provider system allows switching between different AI services easily.
</laravel-boost-guidelines>

<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureCommands();
        $this->configureModels();
        $this->configureVite();
        $this->configureMorphMaps();
        $this->configureHttps();
    }

    /**
     * Configure database commands.
     *
     * Prohibits destructive commands in production environment.
     */
    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands($this->app->isProduction());
    }

    /**
     * Configure Eloquent models.
     *
     * Enables strict mode for models and removes mass assignment protection.
     */
    private function configureModels(): void
    {
        Model::shouldBeStrict();
        Model::unguard();
    }

    /**
     * Configure Vite asset bundling.
     *
     * Sets the prefetch strategy to 'aggressive' for better performance.
     */
    private function configureVite(): void
    {
        Vite::usePrefetchStrategy('aggressive');
    }

    /**
     * Configure Eloquent polymorphic relationships.
     *
     * Maps the 'User' string to the User class for polymorphic relations.
     */
    private function configureMorphMaps(): void
    {
        Relation::morphMap([
            'User' => User::class,
        ]);
    }

    private function configureHttps(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');

            // Trust Expose proxy headers
            $this->app['request']->server->set('HTTPS', 'on');

            // Set trusted proxies for Expose
            Request::setTrustedProxies(['*'],
                Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO
            );
        }
    }
}

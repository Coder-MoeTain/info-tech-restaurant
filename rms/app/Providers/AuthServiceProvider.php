<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        Gate::define('checkout', fn($user) => $user->can('checkout'));
        Gate::define('refund', fn($user) => $user->can('refund'));
        Gate::define('void', fn($user) => $user->can('void'));
        Gate::define('discount', fn($user) => $user->can('discount') || $user->can('manage_menu'));
        Gate::define('reprint', fn($user) => $user->can('reprint'));
        Gate::define('manage_menu', fn($user) => $user->can('manage_menu'));
        Gate::define('manage_tables', fn($user) => $user->can('manage_tables'));
        Gate::define('manage_printers', fn($user) => $user->can('manage_printers'));
        Gate::define('reports', fn($user) => $user->can('reports'));
    }
}

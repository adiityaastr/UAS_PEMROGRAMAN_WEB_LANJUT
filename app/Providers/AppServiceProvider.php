<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('isAdmin', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('isPetugas', function (User $user) {
            return $user->role === 'petugas';
        });

        // Gate untuk fitur yang bisa diakses admin dan petugas
        Gate::define('isPetugasOrAdmin', function (User $user) {
            return $user->role === 'petugas' || $user->role === 'admin';
        });
    }
}

<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('admin', function (User $user) {
            return $user->username === 'admin' || $user->username === 'direksi';

        });
        Gate::define('rm', function (User $user) {
            return $user->username === 'admin'
            || $user->username === 'direksi'
            || $user->username === 'rm'
            || $user->username === 'poli'
            || $user->username === 'casemix'
            || $user->username === 'ipcn'
            || $user->username === 'admin_casemix';
        });
    }
}

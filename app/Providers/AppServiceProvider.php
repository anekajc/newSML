<?php

namespace App\Providers;

use App\Database\LegacySqlServerConnection;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Connection::resolverFor('sqlsrv', function ($connection, $database, $prefix, $config) {
            return new LegacySqlServerConnection($connection, $database, $prefix, $config);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

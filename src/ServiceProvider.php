<?php

namespace Rasulian\UserVerification;

use Rasulian\UserVerification\Repository\VerificationRepository;
use Rasulian\UserVerification\Services\VerificationService;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/verification.php' => $this->app->configPath().'/verification.php',
        ], 'verification-config');

        if (! class_exists('CreateVerificationTables')) {
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__ . '/../database/migrations/create_user_verifications_table.php.stub'
                => $this->app->databasePath()."/migrations/{$timestamp}_create_user_verifications_table.php",
            ], 'verification-migrations');
        }

        // Add the Activation facade
        $this->app->bind('user.verification', function(){
            return new VerificationService(new VerificationRepository());
        });

        // Load the routes
        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

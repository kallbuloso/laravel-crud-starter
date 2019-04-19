<?php

namespace Mvd81\LaravelCrudStarter;

use Illuminate\Support\ServiceProvider;
use File;

class CrudStarterServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot() {

        // Copy the config file to the Laravel config directory.
        if (!File::exists(config_path('crud_starter.php'))) {
            File::copy(__DIR__ . '/Config/crud_starter.php', config_path('crud_starter.php'));
        }

        // Copy the crud source templates to the resources directory.
        if (!File::isDirectory(resource_path('crud-starter-templates'))) {
            File::makeDirectory(resource_path('crud-starter-templates'));
            File::copyDirectory(__DIR__ . '/Templates/', resource_path('crud-starter-templates'));
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

        // Register the artisan command to create a starter crud.
        $commands = [
            'Mvd81\LaravelCrudStarter\Commands\createCrud'
        ];

        $this->commands($commands);
    }
}

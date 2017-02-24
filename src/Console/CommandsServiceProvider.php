<?php

namespace Soda\Cms\Console;

use Soda\Cms\Console\Commands\Seed;
use Soda\Cms\Console\Commands\Setup;
use Soda\Cms\Console\Commands\Theme;
use Soda\Cms\Console\Commands\Assets;
use Soda\Cms\Console\Commands\Config;
use Soda\Cms\Console\Commands\Update;
use Soda\Cms\Console\Commands\Migrate;
use Illuminate\Support\ServiceProvider;

class CommandsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'Update'  => 'soda.command.update',
        'Assets'  => 'soda.command.assets',
        'Migrate' => 'soda.command.migrate',
        'Seed'    => 'soda.command.seed',
        'Config'  => 'soda.command.config',
    ];

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $devCommands = [
        'Setup' => 'soda.command.setup',
        'Theme' => 'soda.command.theme',
    ];

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands($this->commands);

        $this->registerCommands($this->devCommands);
    }

    /**
     * Register the given commands.
     *
     * @param  array $commands
     *
     * @return void
     */
    protected function registerCommands(array $commands)
    {
        foreach ($commands as $command => $binding) {
            $method = "register{$command}Command";

            call_user_func_array([$this, $method], [$binding]);
        }

        $this->commands(array_values($commands));
    }

    /**
     * Register the command.
     *
     * @param $binding
     */
    protected function registerUpdateCommand($binding)
    {
        $this->app->singleton($binding, function($app) {
            return new Update();
        });
    }

    /**
     * Register the command.
     *
     * @param $binding
     */
    protected function registerAssetsCommand($binding)
    {
        $this->app->singleton($binding, function($app) {
            return new Assets();
        });
    }

    /**
     * Register the command.
     *
     * @param $binding
     */
    protected function registerMigrateCommand($binding)
    {
        $this->app->singleton($binding, function($app) {
            return new Migrate();
        });
    }

    /**
     * Register the command.
     *
     * @param $binding
     */
    protected function registerSeedCommand($binding)
    {
        $this->app->singleton($binding, function($app) {
            return new Seed();
        });
    }

    /**
     * Register the command.
     *
     * @param $binding
     */
    protected function registerConfigCommand($binding)
    {
        $this->app->singleton($binding, function($app) {
            return new Config();
        });
    }

    /**
     * Register the command.
     *
     * @param $binding
     */
    protected function registerSetupCommand($binding)
    {
        $this->app->singleton($binding, function($app) {
            return new Setup($app['config'], $app['db']);
        });
    }

    /**
     * Register the command.
     *
     * @param $binding
     */
    protected function registerThemeCommand($binding)
    {
        $this->app->singleton($binding, function($app) {
            return new Theme();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        if ($this->app->environment('production')) {
            return array_values($this->commands);
        } else {
            return array_merge(array_values($this->commands), array_values($this->devCommands));
        }
    }
}

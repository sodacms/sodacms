<?php

namespace Soda\Cms\Foundation\Routing;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use Soda\Cms\Http\Middleware\Authenticate;
use Soda\Cms\Http\Middleware\Drafting;
use Soda\Cms\Http\Middleware\Security;
use Soda\Cms\Http\Middleware\HasAbility;
use Soda\Cms\Http\Middleware\HasPermission;
use Soda\Cms\Http\Middleware\HasRole;
use Soda\Cms\Http\Middleware\RedirectIfAuthenticated;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Soda\Cms\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $router = $this->app['router'];

        $router->middlewareGroup('soda.web', [
            Drafting::class,
            Security::class,
        ]);

        $router->middlewareGroup('soda.api', [
            Drafting::class,
        ]);

        $router->middleware('soda.auth', Authenticate::class);
        $router->middleware('soda.guest', RedirectIfAuthenticated::class);

        $router->middleware('soda.role', HasRole::class);
        $router->middleware('soda.permission', HasPermission::class);
        $router->middleware('soda.ability', HasAbility::class);

        parent::boot();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $coreRouter = $this->app['router'];
        $coreRouter->dispatch = function(Request $request)
        {
            return $this->app['router']->dispatch($request);
        };
        $coreRouter->gatherRouteMiddleware = function(IlluminateRoute $route)
        {
            return $this->app['router']->gatherRouteMiddleware($route);
        };

        // If the router is "rebound", we will need to rebuild the middleware.
        // by copying properties from the existing router instance

        $this->app->rebinding('router', function ($app, $router) use ($coreRouter){
            $reflectionRouter = new ReflectionClass($coreRouter);
            $property = $reflectionRouter->getProperty('middlewareGroups');
            $property->setAccessible(true);
            $middlewareGroups = $property->getValue($coreRouter);

            $router->middlewarePriority = $coreRouter->middlewarePriority;

            foreach ($middlewareGroups as $key => $middleware) {
                $router->middlewareGroup($key, $middleware);
            }

            foreach ($coreRouter->getMiddleware() as $key => $middleware) {
                $router->middleware($key, $middleware);
            }

            $app->instance('routes', $router->getRoutes());
            \Route::clearResolvedInstance('router');
        });

        $this->app->singleton('router', function ($app) {
            return new Router($app['events'], $app);
        });

        $this->app->alias('router', 'Illuminate\Contracts\Routing\Registrar');
        $this->app->alias('router', 'Illuminate\Routing\Router');
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => ['soda.web'],
            'namespace'  => $this->namespace,
        ], function ($router) {
            require(__DIR__.'/../../../routes/web.php');
        });

        $this->app['router']->getRoutes()->refreshNameLookups();

        $this->app['events']->fire('soda.routing', $this->app->router);
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => ['api'],
            'namespace'  => $this->namespace,
            'prefix'     => config('soda.cms.path').'/api',
        ], function ($router) {
            require(__DIR__.'/../../../routes/api.php');
        });
    }
}

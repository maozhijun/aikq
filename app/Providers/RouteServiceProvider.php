<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapAdminRoutes();
        $this->mapBackstageRoutes();

        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapMipRoutes();

        $this->mapIntFRoutes();
        //
        $this->mapAPPRoutes();
        //
        $this->mapAPPV120Routes();
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
        Route::middleware('web')
            ->prefix('m')
            ->namespace($this->namespace . '\Mobile')
            ->group(base_path('routes/mobile.php'));

        Route::middleware('web')
             ->namespace($this->namespace . '\PC')
             ->group(base_path('routes/pc.php'));

        Route::middleware('web')
            ->prefix('db')
            ->namespace($this->namespace . '\DB')
            ->group(base_path('routes/web.php'));
    }
    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapMipRoutes()
    {
        Route::middleware('web')
            ->prefix('mip')
            ->namespace($this->namespace . '\Mip')
            ->group(base_path('routes/mip.php'));
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
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    protected function mapAdminRoutes()
    {
        Route::prefix('admin')
            ->middleware('web')
            ->namespace($this->namespace .'\Admin')
            ->group(base_path('routes/admin.php'));
    }

    protected function mapIntFRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace .'\IntF')
            ->group(base_path('routes/web.php'));
    }

    protected function mapAPPRoutes(){
        Route::prefix('app/v110')
            ->middleware('api')
            ->namespace($this->namespace .'\PC')
            ->group(base_path('routes/app/v110.php'));
    }

    protected function mapAPPV120Routes(){
        Route::prefix('app/v120')
            ->middleware('api')
            ->namespace($this->namespace .'\PC')
            ->group(base_path('routes/app/v120.php'));
    }

    protected function mapBackstageRoutes(){
        Route::prefix('bs')
            ->middleware('web')
            ->namespace($this->namespace .'\Backstage')
            ->group(base_path('routes/backstage.php'));
    }
}

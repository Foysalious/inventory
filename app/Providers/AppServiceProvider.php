<?php

namespace App\Providers;

use App\Sheba\DataMigration\DataMigrationServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
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
        $this->app->register(DataMigrationServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('l5-swagger.swagger_on_dev') == true){
            URL::forceScheme('https');
        }
        JsonResource::withoutWrapping();
    }
}

<?php namespace App\Providers;

use App\Interfaces\OptionRepositoryInterface;
use App\Repositories\OptionRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
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
        $this->app->singleton(OptionRepositoryInterface::class, OptionRepository::class);
    }

}

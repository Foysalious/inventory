<?php namespace App\Providers;

use App\Interfaces\OptionRepositoryInterface;
use App\Interfaces\ValueRepositoryInterface;
use App\Interfaces\CollectionRepositoryInterface;
use App\Repositories\OptionRepository;
use App\Repositories\ValueRepository;
use App\Repositories\CollectionRepository;
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
        $this->app->singleton(ValueRepositoryInterface::class, ValueRepository::class);
        $this->app->singleton(CollectionRepositoryInterface::class, CollectionRepository::class);
    }

}

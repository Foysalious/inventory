<?php namespace App\Providers;

use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\OptionRepositoryInterface;
use App\Interfaces\UnitRepositoryInterface;
use App\Interfaces\PartnerCategoryRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\ValueRepositoryInterface;
use App\Interfaces\CollectionRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\OptionRepository;
use App\Repositories\UnitRepository;
use App\Repositories\PartnerCategoryRepository;
use App\Repositories\ProductRepository;
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
        $this->app->singleton(UnitRepositoryInterface::class, UnitRepository::class);
        $this->app->singleton(ValueRepositoryInterface::class, ValueRepository::class);
        $this->app->singleton(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->singleton(CollectionRepositoryInterface::class, CollectionRepository::class);
        $this->app->singleton(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->singleton(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->singleton(PartnerCategoryRepositoryInterface::class, PartnerCategoryRepository::class);
    }

}

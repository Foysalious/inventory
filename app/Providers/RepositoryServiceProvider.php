<?php namespace App\Providers;


use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\DiscountRepositoryInterface;
use App\Interfaces\OptionRepositoryInterface;
use App\Interfaces\UnitRepositoryInterface;
use App\Interfaces\CategoryPartnerRepositoryInterface;
use App\Interfaces\ProductImageRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\ValueRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Interfaces\CollectionRepositoryInterface;
use App\Repositories\DiscountRepository;
use App\Repositories\OptionRepository;
use App\Repositories\UnitRepository;
use App\Repositories\CategoryPartnerRepository;
use App\Repositories\ProductImageRepository;
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
        $this->app->singleton(CategoryPartnerRepositoryInterface::class, CategoryPartnerRepository::class);
        $this->app->singleton(CollectionRepositoryInterface::class, CollectionRepository::class);
        $this->app->singleton(ProductImageRepositoryInterface::class, ProductImageRepository::class);
        $this->app->singleton(DiscountRepositoryInterface::class, DiscountRepository::class);
    }

}

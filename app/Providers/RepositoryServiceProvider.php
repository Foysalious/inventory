<?php namespace App\Providers;


use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\CombinationRepositoryInterface;
use App\Interfaces\DiscountRepositoryInterface;
use App\Interfaces\OptionRepositoryInterface;
use App\Interfaces\ProductChannelRepositoryInterface;
use App\Interfaces\ProductOptionRepositoryInterface;
use App\Interfaces\ProductOptionValueRepositoryInterface;
use App\Interfaces\SkuChannelRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Interfaces\PartnerRepositoryInterface;
use App\Interfaces\ProductUpdateLogRepositoryInterface;
use App\Interfaces\UnitRepositoryInterface;
use App\Interfaces\CategoryPartnerRepositoryInterface;
use App\Interfaces\ProductImageRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\ValueRepositoryInterface;
use App\Interfaces\ChannelRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Interfaces\CollectionRepositoryInterface;
use App\Repositories\CombinationRepository;
use App\Repositories\DiscountRepository;
use App\Repositories\OptionRepository;
use App\Repositories\ProductChannelRepository;
use App\Repositories\ProductOptionRepository;
use App\Repositories\ProductOptionValueRepository;
use App\Repositories\SkuChannelRepository;
use App\Repositories\SkuRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\ProductUpdateLogRepository;
use App\Repositories\UnitRepository;
use App\Repositories\CategoryPartnerRepository;
use App\Repositories\ProductImageRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ValueRepository;
use App\Repositories\CollectionRepository;
use App\Repositories\ChannelRepository;
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
        $this->app->singleton(ChannelRepositoryInterface::class, ChannelRepository::class);
        $this->app->singleton(ProductOptionRepositoryInterface::class, ProductOptionRepository::class);
        $this->app->singleton(ProductOptionValueRepositoryInterface::class, ProductOptionValueRepository::class);
        $this->app->singleton(CombinationRepositoryInterface::class, CombinationRepository::class);
        $this->app->singleton(ProductChannelRepositoryInterface::class, ProductChannelRepository::class);
        $this->app->singleton(SkuRepositoryInterface::class, SkuRepository::class);
        $this->app->singleton(PartnerRepositoryInterface::class, PartnerRepository::class);
        $this->app->singleton(ProductUpdateLogRepositoryInterface::class, ProductUpdateLogRepository::class);
        $this->app->singleton(SkuChannelRepositoryInterface::class, SkuChannelRepository::class);
    }

}

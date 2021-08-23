<?php namespace App\Providers;

use App\Events\ProductStockAdded;
use App\Events\ProductStockUpdated;
use App\Events\RewardOnProductCreate as RewardOnProductCreateEvent;
use App\Listeners\AccountingEntryOnProductStockAdded;
use App\Listeners\AccountingEntryOnProductStockUpdated;
use App\Listeners\RewardOnProductCreate as RewardOnProductCreateListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ProductStockAdded::class => [
            AccountingEntryOnProductStockAdded::class
        ],
        ProductStockUpdated::class => [
            AccountingEntryOnProductStockUpdated::class
        ],
        RewardOnProductCreateEvent::class => [
            RewardOnProductCreateListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

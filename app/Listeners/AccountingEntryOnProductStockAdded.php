<?php

namespace App\Listeners;

use App\Events\ProductStockAdded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AccountingEntryOnProductStockAdded
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ProductStockAdded  $event
     * @return void
     */
    public function handle(ProductStockAdded $event)
    {
        //
    }
}

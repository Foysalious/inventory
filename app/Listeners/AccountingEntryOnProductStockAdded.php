<?php

namespace App\Listeners;

use App\Events\ProductStockAdded;
use App\Services\Accounting\ExpenseEntry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AccountingEntryOnProductStockAdded
{
    protected ExpenseEntry $stockEntry;

    /**
     * AccountingEntryOnProductStockAdded constructor.
     * @param ExpenseEntry $stockEntry
     */
    public function __construct(ExpenseEntry $stockEntry)
    {
        $this->stockEntry = $stockEntry;
    }


    /**
     * Handle the event.
     *
     * @param  ProductStockAdded  $event
     * @return void
     */
    public function handle(ProductStockAdded $event)
    {
        $this->stockEntry->setProduct($event->getProduct())->setData($event->getRequest())->createEntryForStockAdd();
    }
}

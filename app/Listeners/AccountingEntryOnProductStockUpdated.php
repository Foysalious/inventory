<?php

namespace App\Listeners;

use App\Events\ProductStockUpdated;
use App\Services\Accounting\ExpenseEntry;


class AccountingEntryOnProductStockUpdated
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
     * @param  ProductStockUpdated  $event
     * @return void
     */
    public function handle(ProductStockUpdated $event)
    {
        $this->stockEntry->setProduct($event->getProduct())->setData($event->getRequest())->createEntryForProductStockAdd();
    }
}

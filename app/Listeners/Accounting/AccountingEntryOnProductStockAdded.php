<?php namespace App\Listeners\Accounting;

use App\Events\Accounting\ProductStockAdded;
use App\Services\Accounting\StockAddEntry;

class AccountingEntryOnProductStockAdded
{
    protected StockAddEntry $stockEntry;

    /**
     * AccountingEntryOnProductStockAdded constructor.
     * @param StockAddEntry $stockEntry
     */
    public function __construct(StockAddEntry $stockEntry)
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
        $this->stockEntry->setProduct($event->getProduct())->setData($event->getRequestObject())->createEntry();
    }
}

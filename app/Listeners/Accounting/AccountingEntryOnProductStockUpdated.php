<?php namespace App\Listeners\Accounting;

use App\Events\Accounting\ProductStockUpdated;
use App\Services\Accounting\StockUpdateEntry;


class AccountingEntryOnProductStockUpdated
{
    protected StockUpdateEntry $stockEntry;

    /**
     * AccountingEntryOnProductStockUpdated constructor.
     * @param StockUpdateEntry $stockEntry
     */
    public function __construct(StockUpdateEntry $stockEntry)
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
        $this->stockEntry->setProduct($event->getProduct())->setData($event->getRequest())->setOldStockData($event->getOldStockData())->createEntry();
    }
}

<?php namespace App\Services\Accounting;

use App\Helper\Miscellaneous\RequestIdentification;
use App\Models\Order;
use App\Repositories\Accounting\AccountingRepository;
use App\Repositories\Accounting\Constants\EntryTypes;
use App\Services\Accounting\Constants\Accounts;
use App\Services\Accounting\Constants\Cash;
use App\Services\Accounting\Constants\Sales;
use App\Services\Inventory\InventoryServerClient;
use App\Services\Order\Constants\SalesChannel;
use App\Services\Order\Constants\SalesChannelIds;
use App\Services\Order\PriceCalculation;
use App\Traits\ModificationFields;
use Illuminate\Support\Facades\App;

class StockEntry
{
    use ModificationFields;
    protected AccountingRepository $accountingRepository;

    /**
     * Creator constructor.
     * @param AccountingRepository $accountingRepository
     */
    public function __construct(AccountingRepository $accountingRepository)
    {
        $this->accountingRepository = $accountingRepository;
    }


    public function entryOnStockAdd()
    {

    }

    public function entryOnStockUpdate()
    {

    }

    private function makeData()
    {

    }


}

<?php namespace App\Services\Accounting;


use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Repositories\Accounting\AccountingRepository;
use App\Services\Accounting\Constants\EntryTypes;
use App\Traits\ModificationFields;

class ExpenseEntry
{
    use ModificationFields;
    protected AccountingRepository $accountingRepository;
    protected Product $product;
    protected array $productDetails;
    protected array $accountingInfo;

    /**
     * StockEntry constructor.
     * @param AccountingRepository $accountingRepository
     */
    public function __construct(AccountingRepository $accountingRepository)
    {
        $this->accountingRepository = $accountingRepository;
    }


    /**
     * @param ProductRequest $request
     * @return $this
     */
    public function setData(ProductRequest $request)
    {
        $this->productDetails = (json_decode($request->product_details,true))[0];
        $this->accountingInfo = json_decode($request->accounting_info,true);
        return $this;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    public function createEntryForStockAdd()
    {
        $data = $this->makeDataForAddingStock();
        $this->accountingRepository->storeEntry($data, $this->product->partner_id );
    }

    public function createEntryForStockUpdate()
    {

    }

    private function makeDataForAddingStock()
    {
        $data['partner'] = $this->product->partner_id;
        $data['amount'] = $this->productDetails['stock'] * $this->productDetails['channel_data'][0]['cost'];
        $data['from_account_key'] = $this->accountingInfo['from_account'];
        $data['to_account_key'] = $this->product->id;
        $data['customer_id'] = $this->accountingInfo['supplier_id'];
        $data['inventory_products'] = [
            [
                'id' => $this->product->id,
                'unit_price' => $this->productDetails['channel_data'][0]['cost'],
                'name' => $this->product->name,
                'quantity' => $this->productDetails['stock']
            ]
        ];
        if ($this->accountingInfo['transaction_type'] == 'due')
            $data['amount_cleared'] = $this->accountingInfo['amount_cleared'];
        $data['source_id'] = null;
        return $data;

    }

    private function makeDataForUpdatingStock()
    {
        return [];
    }






}

<?php namespace App\Services\Accounting;

use App\Helper\Miscellaneous\RequestIdentification;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Repositories\Accounting\AccountingRepository;
use App\Services\Accounting\Constants\EntryTypes;
use App\Traits\ModificationFields;
use Illuminate\Http\Request;


class BaseEntry
{
    use ModificationFields;
    protected AccountingRepository $accountingRepository;
    protected Product $product;
    protected array $productDetails;
    protected array $accountingInfo;
    protected array $data;


    /**
     * Creator constructor.
     * @param AccountingRepository $accountingRepository
     */
    public function __construct(AccountingRepository $accountingRepository,)
    {
        $this->accountingRepository = $accountingRepository;
    }

    /**
     * @param ProductRequest $request
     * @return $this
     */
    public function setData(Request $request)
    {
        $this->productDetails = (json_decode($request->product_details,true))[0];
        $this->accountingInfo = json_decode($request->accounting_info,true);
        return $this;
    }

    /**
     * @param Product $product
     * @return BaseEntry
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    public function makeCommonData() : array {

        $data = [
            'created_from' => json_encode($this->withBothModificationFields((new RequestIdentification())->get())),
            'credit_account_key' => $this->accountingInfo['from_account'],
            'debit_account_key'  => $this->product->id,
            'note' =>  $this->accountingInfo['note'] ?? null,
            'source_type'        => EntryTypes::INVENTORY,
            'total_discount'     => (double) isset($this->accountingInfo['total_discount']) ? $this->accountingInfo['total_discount'] :  null ,
            'total_vat'          => (double) isset($this->accountingInfo['total_vat']) ? $this->accountingInfo['total_vat'] :  null,
            'entry_at' => $this->accountingInfo['date'] ?? $this->product->created_at->format('Y-m-d H:i:s'),
            'customer_id' =>    $this->accountingInfo['supplier_id'],
            'customer_name' => $this->accountingInfo['supplier_name'] ?? null,
            'partner' => $this->product->partner_id,
        ];
        return  $data;
    }

    protected function makeData(): array
    {
        $data = $this->makeCommonData();
        list($inventory_items,$total_amount) = $this->makeInventoryProduct();
        $data['partner'] = $this->product->partner_id;
        $data['inventory_products'] = $inventory_items;
        $data['amount'] = $total_amount;
        if ($this->accountingInfo['transaction_type'] == 'due')
            $data['amount_cleared'] = $this->accountingInfo['amount_cleared'];
        $data['source_id'] = null;
        return $data;

    }

    protected function makeInventoryProduct():array
    {
        $data = [];
        $total_amount = 0;
        $this->product->skus()->each(function ($each_sku) use (&$data,&$total_amount){
            $batches = $each_sku->batch()->get();
            foreach ($batches as $batch) {
                $data [] = [
                    'id' => $this->product->id,
                    'sku_id' => $batch->sku_id,
                    'unit_price' => (double) $batch->cost,
                    'name' => $this->product->name,
                    'quantity' => (double) $batch->stock
                ];
                $total_amount = $total_amount + ($batch->cost * $batch->stock);
            }
        });
        return [ json_encode($data), $total_amount ];
    }

}

<?php namespace App\Services\Accounting;


class StockUpdateEntry extends BaseEntry
{

    protected ?array $oldStockData;

    public function createEntry()
    {
        $data = $this->makeData();
        if(!is_null($this->oldStockData)) {
            $this->makeNegativeAccountingEntry($data);
        }
        $this->accountingRepository->storeEntry($data, $this->product->partner_id );
    }


    /**
     * @param array|null $oldStockData
     */
    public function setOldStockData(?array $oldStockData)
    {
        $this->oldStockData = $oldStockData;
        return $this;
    }

    private function makeNegativeAccountingEntry(array $data)
    {
        $data['debit_account_key']   = $this->oldStockData['from_account'];
        $data['credit_account_key']  = $this->product->id;
        $data['amount']              = $this->oldStockData['total_cost'];
        $data['customer_id']         = $this->oldStockData['supplier_id'];
        $data['inventory_products']  = json_encode($this->oldStockData['returned_stock']);
        $data['amount_cleared']      = $this->oldStockData['total_cost'];

        $this->accountingRepository->storeEntry($data, $this->product->partner_id);
    }
}

<?php namespace App\Services\Accounting;


class ExpenseEntry extends BaseEntry
{

    public function createEntryForProductStockAdd()
    {
        $data = $this->makeDataForStockAdd();
        $this->accountingRepository->storeEntry($data, $this->product->partner_id );
    }

    private function makeDataForStockAdd(): array
    {
        $data = $this->makeCommonData();
        $data['partner'] = $this->product->partner_id;
        $data['amount'] = $this->productDetails['stock'] * $this->productDetails['channel_data'][0]['cost'];
        $data['inventory_products'] = json_encode([
            [
                'id' => $this->product->id,
                'unit_price' => (double) $this->productDetails['channel_data'][0]['cost'],
                'name' => $this->product->name,
                'quantity' => (double) $this->productDetails['stock']
            ]
        ]);
        if ($this->accountingInfo['transaction_type'] == 'due')
            $data['amount_cleared'] = $this->accountingInfo['amount_cleared'];
        $data['source_id'] = null;
        return $data;

    }

    private function makeDataForStockUpdate(): array
    {
        $data = $this->makeCommonData();
        $data['amount'] = $this->productDetails['stock'] * $this->productDetails['channel_data'][0]['cost'];
        $data['inventory_products'] = $this->makeInventoryProduct();
        if ($this->accountingInfo['transaction_type'] == 'due')
            $data['amount_cleared'] = $this->accountingInfo['amount_cleared'];
        $data['source_id'] = null;
        return $data;

    }

    private function makeInventoryProduct():string
    {
        foreach ($this->productDetails as $each_sku) {

        }
    }


}

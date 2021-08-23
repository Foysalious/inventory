<?php namespace App\Services\Accounting;

use App\Http\Requests\SkuStockAddRequest;

class StockAddEntry extends BaseEntry
{

    public function createEntry()
    {
        $data = $this->makeData();

        if (get_class($this->requestObject) == SkuStockAddRequest::class) {
            $data = $this->makeDataForIndividualStockAdd($data);
        }
        $this->accountingRepository->storeEntry($data, $this->product->partner_id);


    }

    private function makeDataForIndividualStockAdd($data)
    {
        $inventory = [
            'id' => $this->product->id,
            'quantity' => (double) $this->requestObject->stock,
            'unit_price' => (double) $this->requestObject->cost
        ];
        $data['amount'] = (double) $this->requestObject->stock * $this->requestObject->cost;
        $data['inventory_products'] = json_encode([$inventory]);
        return $data;
    }


}

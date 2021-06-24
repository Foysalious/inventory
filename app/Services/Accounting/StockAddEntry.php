<?php namespace App\Services\Accounting;


class StockAddEntry extends BaseEntry
{

    public function createEntry()
    {
        $data = $this->makeData();
        $this->accountingRepository->storeEntry($data, $this->product->partner_id );
    }



}

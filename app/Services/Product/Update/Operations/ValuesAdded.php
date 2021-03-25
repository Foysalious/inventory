<?php namespace App\Services\Product\Update\Operations;


class ValuesAdded extends ValuesUpdated
{
    public function apply()
    {
        $this->operationsForValueAdd();
        $this->resolveProductChannel();
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductChannelPriceResource extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sku_id' => $this->sku_id,
            'channel_id' => $this->channel_id,
            'cost' => $this->cost,
            'price' => $this->price,
            'wholesale_price' => $this->wholesale_price
        ];
    }
}

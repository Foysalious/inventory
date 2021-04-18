<?php namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class SkuResource extends JsonResource
{
    public function toArray($request)
    {

        return [

            'name'  => $this->name,
            'product_id' => $this->product_id,
            'warranty' => $this->warranty,
            'warranty_unit' => $this->warranty_unit,
            'vat_percentage' => $this->vat_percentage,
            'stock' => $this->stock,
            'sku_channel' => $this->skuChannels
        ];
    }

}

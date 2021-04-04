<?php namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class SkuResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id' => $this->id,
            'product_id' => $this->product->id,
            'warranty' => $this->product->warranty,
            'warranty_unit' => $this->product->warranty_unit,
            'vat_percentage' => $this->product->vat_percentage,
            'stock' => $this->stock,
            'sku_channel' => $this->skuChannels
        ];
    }

}

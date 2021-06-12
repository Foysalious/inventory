<?php namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class SkuResource extends JsonResource
{
    public function toArray($request)
    {

        return [

            'id' => $this->id,
            'name'  => $this->name,
            'product_id' => $this->product_id,
            'warranty' => $this->warranty,
            'warranty_unit' => $this->warranty_unit,
            'sku_channel' => $this->skuChannels,
            'stock' => $this->stock,


//            'id' => $this->id,
//            'product_name'  => $this->product->name,
//            'product_id' => $this->product_id,
//            'warranty' => $this->product->warranty,
//            'warranty_unit' => $this->product->warranty_unit,
//            'vat_percentage' => $this->product->vat_percentage,
//            'unit' => $this->product->unit,
//            'stock' => $this->stock,
//            'sku_channel' => $this->skuChannels,
//            'combination' => $this->sku_details->combinations
        ];
    }

}

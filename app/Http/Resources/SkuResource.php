<?php namespace App\Http\Resources;


use App\Models\Sku;
use Illuminate\Http\Resources\Json\JsonResource;

class SkuResource extends JsonResource
{
    public function toArray($request)
    {
        /** @var $this Sku */
        return [

            'id' => $this->id,
            'product_name'  => $this->product->name,
            'product_id' => $this->product_id,
            'warranty' => $this->product->warranty,
            'warranty_unit' => $this->product->warranty_unit,
            'vat_percentage' => $this->product->vat_percentage,
            'unit' => $this->product->unit,
            'stock' => $this->stock(),
            'sku_channel' => $this->skuChannels()->with('discounts')->get(),
            'combination' => $this->sku_details->combinations ?? [],
            'batches' => $this->batch()->selectRaw('id as batch_id,stock,cost')->orderBy('id')->get(),
        ];
    }

}

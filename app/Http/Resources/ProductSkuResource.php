<?php namespace App\Http\Resources;
use App\Models\Discount;
use App\Models\SkuChannel;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSkuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $sku_id = $this->skus()->pluck('id')->first();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'original_price'=>SkuChannel::all()->where('sku_id',$sku_id)->min('price'),
            'vat_included_price'=> 200 ,
            'vat_percentage' => $this->vat_percentage,
            'unit' => Unit::all()->where('id',$this->unit_id)->pluck('name_en')->first(),
            'stock' => $this->skus()->pluck('stock')->first(),
            'category_id' => $this->category_id,
            'discount_applicable' => 0,
            'discounted_amount' => Discount::all()->where('type_id',$this->id)->pluck('amount')->first(),
            'discount_percentage' => Discount::all()->where('type_id',$this->id)->pluck('is_amount_percentage')->first(),
            'rating' => 5,
            'count_rating' => 72,
            'icon' => 'sdsd',
            'thumb' => 'thumb',
            'banner' => 'banner'



        ];
    }
}

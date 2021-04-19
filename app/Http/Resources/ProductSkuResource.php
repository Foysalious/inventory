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
            'original_price'=>500,
            'vat_included_price'=> 200 ,
            'vat_percentage' => $this->vat_percentage,
            'unit' => $this->unit()->pluck('name_en')->first(),
            'stock' => $this->skus()->pluck('stock')->first(),
            'category_id' => $this->category_id,
            'discount_applicable' => 0,
            'discounted_amount' => 10,
            'discount_percentage' => 3.33,
            'rating' => 5,
            'count_rating' => 72,
            'icon' => 'sdsd',
            'thumb' => 'thumb',
            'banner' => 'banner'



        ];
    }
}

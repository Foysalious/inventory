<?php namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $original_price = $this->getOriginalPrice();
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,
            'original_price' =>  $original_price,
            'vat_included_price' => $original_price + ($original_price * $this->vat_percentage) / 100,
            'vat_percentage' => $this->vat_percentage,
            'unit' => $this->unit ?: null,
            'stock' => $this->stock,
            'discount_applicable' => 1,
            'discounted_amount' => $this->getDiscountedAmount(),
            'discount_percentage' => $this->getDiscountPercentage(),
            'rating' => 5,
            'count_rating' => 7,
            'app_thumb'=> "https://s3.ap-south-1.amazonaws.com/cdn-shebadev/images/pos/services/thumbs/1608693744_jacket.jpeg",
            'warranty' => $this->warranty,
            'warranty_unit' => $this->warranty_unit,
            'options' => $this->options,
            'combinations' => $this->combinations,
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Services\Product\ProductCombinationService;
use Illuminate\Http\Resources\Json\JsonResource;

class PosProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $original_price =  $this->getOriginalPrice();
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'master_category_id' => $this->getMasterCategory($this->category),
            'name' => $this->name,
            'original_price' =>   $original_price,
            'vat_included_price' => $original_price + ($original_price * $this->vat_percentage) / 100,
            'vat_percentage' => $this->vat_percentage,
            'unit' => $this->unit ?: null,
            'stock' => $this->stock,
            'discount_applicable' => 1,
            'discounted_amount' => $this->getDiscountedAmount(),
            'discount_percentage' => $this->getDiscountPercentage(),
            'app_thumb'=> "https://s3.ap-south-1.amazonaws.com/cdn-shebadev/images/pos/services/thumbs/1608693744_jacket.jpeg",
            'combinations' => $this->combinations(),
            'created_at' => $this->created_at
        ];
    }

    private function getMasterCategory($category)
    {
        if ($category->parent_id == null) return $category->id;
        $parent = Category::withTrashed()->find($category->parent_id);
        return $this->getMasterCategory($parent);
    }
}

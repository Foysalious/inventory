<?php namespace App\Http\Resources\Webstore;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResource extends JsonResource
{
    public function toArray($request)
    {
        list($rating,$count_rating) = $this->getRatingandCount();
        list($discounted_price_with_vat, $discount_percentage) = $this->getDiscountedPriceWithVat();

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'collection_id' => $this->collectionIds(),
            'name' => $this->name,
            'rating' => $rating,
            'rating_count' => $count_rating,
            'app_thumb'=> $this->app_thumb,
            'original_price'=> (double) $this->getOriginalPrice(),
            'discounted_price' => (double) $discounted_price_with_vat,
            'discount_percentage' => $discount_percentage
        ];
    }

}

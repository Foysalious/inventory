<?php namespace App\Http\Resources\Webstore;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResource extends JsonResource
{
    public function toArray($request)
    {
        list($rating,$count_rating) = $this->getRatingandCount();

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'collection_id' => $this->collectionIds(),
            'name' => $this->name,
            'rating' => $rating,
            'count_rating' => $count_rating,
            'app_thumb'=> $this->app_thumb,
            'options' => $this->options,
            'original_price'=> (double) $this->getOriginalPriceWithVat(),
            'discounted_price' => (double) $this->getDiscountedPriceWithVat()
        ];
    }

}

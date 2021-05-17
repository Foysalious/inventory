<?php namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebstoreProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        list($rating,$count_rating) = $this->getRatingandCount();
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'collection_id' => $this->collection_id,
            'name' => $this->name,
            'description' => $this->description,
            'vat_percentage' => $this->vat_percentage,
            'unit' => $this->unit ?: null,
            'stock' => $this->stock,
            'rating' => $rating,
            'count_rating' => $count_rating,
            'app_thumb'=> $this->app_thumb,
            'warranty' => $this->warranty,
            'warranty_unit' => $this->warranty_unit,
            'options' => $this->options,
            'orginal_price'=> $this->getOriginalPrice(),
            'variations' => $this->combinations(),
            'created_at' => $this->created_at
        ];
    }
}

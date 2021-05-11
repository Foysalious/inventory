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
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'collection_id' => $this->collection_id,
            'name' => $this->name,
            'description' => $this->description,
            'vat_percentage' => $this->vat_percentage,
            'unit' => $this->unit ?: null,
            'stock' => $this->stock,
            'rating' => 5,
            'count_rating' => 7,
            'app_thumb'=> "https://s3.ap-south-1.amazonaws.com/cdn-shebadev/images/pos/services/thumbs/1608693744_jacket.jpeg",
            'warranty' => $this->warranty,
            'warranty_unit' => $this->warranty_unit,
            'options' => $this->options,
            'combinations' => $this->combinations(),
            'created_at' => $this->created_at
        ];
    }
}

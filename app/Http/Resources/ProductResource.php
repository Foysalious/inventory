<?php namespace App\Http\Resources;

use App\Models\Product;
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
        /** @var $this Product */
        return [
            'id' => $this->id,
            'category_id' => $this->resource->category->parent->id ?? null,
            'sub_category_id' => $this->category_id,
            'name' => $this->name,
            'vat_percentage' => $this->vat_percentage,
            'unit' => $this->unit ?: null,
            'stock' => $this->getTotalStock(),
            'app_thumb'=> "https://s3.ap-south-1.amazonaws.com/cdn-shebadev/images/pos/services/thumbs/1608693744_jacket.jpeg",
            'variations' => $this->combinations(),
            'created_at' => $this->created_at
        ];
    }
}

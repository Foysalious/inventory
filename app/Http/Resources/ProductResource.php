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
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,
            'warranty' => $this->warranty,
            'warranty_unit' => $this->warranty_unit,
            'vat_percentage' => $this->vat_percentage,
            'unit' => $this->unit ?: null,
            'combinations' => $this->combinations,
        ];
    }
}

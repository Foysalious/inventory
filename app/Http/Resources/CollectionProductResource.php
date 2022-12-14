<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollectionProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'description'   => $this->description,
            'thumb'         => $this->thumb,
            'banner'        => $this->banner,
            'app_thumb'     => $this->app_thumb,
            'app_banner'    => $this->app_banner,
            'is_published'  => $this->is_published,
            'products'      => ProductResource::collection($this->products)
        ];
    }
}

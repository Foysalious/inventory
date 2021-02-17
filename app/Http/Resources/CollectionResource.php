<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'collection_id' => $this->id,

            'collection_name' => $this->name,

            'collection_description' => $this->description,

            'collection_thumb' => $this->thumb,

            'collection_banner' => $this->banner,

            'collection_app_thumb' => $this->app_thumb,

            'collection_app_banner' => $this->app_banner,

            'collection_is_published' => $this->is_published
        ];
    }
}

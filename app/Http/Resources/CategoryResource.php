<?php namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function __construct($resource) {
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'app_thumb'=>$this->thumb,
            'is_default' => $this->categoryPartner()->get()->pluck('is_default')->first(),
            'is_published_for_sheba' => $this->is_published_for_sheba,
            'sub_categories' => $this->children,
        ];
    }


}

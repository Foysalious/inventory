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
            'icon'=>$this->thumb,
            'sub_categories' => $this->children,
        ];
    }


}

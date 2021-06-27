<?php namespace App\Http\Resources\Webstore;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSearchResultResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'partner_id' => $this->partner_id,
            'category_id' => $this->category_id,
        ];
    }

}

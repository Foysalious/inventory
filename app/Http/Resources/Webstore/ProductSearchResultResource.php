<?php namespace App\Http\Resources\Webstore;


use App\Models\Product;
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
        /** @var $this Product */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'total_stock' => $this->stock(),
            'description' => $this->description,
            'partner_id' => $this->partner_id,
            'category_id' => $this->category_id,
        ];
    }

}

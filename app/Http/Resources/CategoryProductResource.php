<?php namespace App\Http\Resources;


use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryProductResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'total_items' => $this->total_items,
            'total_buying_price' => $this->total_buying_price,
            'items_with_buying_price' => $this->items_with_buying_price,
            'products' => PosProductResource::collection($request->products),
        ];
    }
}

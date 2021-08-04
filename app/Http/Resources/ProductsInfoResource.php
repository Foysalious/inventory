<?php namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsInfoResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $products = ProductResource::collection($this->products);
        return [
            'total_products' => $this->total_products,
            'total_products_with_variation' => $this->total_products_with_variation,
            'total_buying_price' => $this->total_buying_price,
            'items_with_buying_price' => $this->items_with_buying_price,
            'products' => $products,
            'deleted_products' => $this->deleted_products,
        ];
    }
}

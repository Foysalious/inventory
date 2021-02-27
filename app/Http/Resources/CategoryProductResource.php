<?php namespace App\Http\Resources;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryProductResource extends JsonResource
{
    private $categoryProducts;

    public function setProducts($categoryProducts)
    {
        $this->categoryProducts = $categoryProducts;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'products' => ProductResource::collection($this->categoryProducts),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

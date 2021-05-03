<?php namespace App\Http\Resources;


use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use phpDocumentor\Reflection\Types\Collection;

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
        $products = PosProductResource::collection($request->products);
        list($total_buying_price, $items_with_buying_price) = $this->getTotalBuyingPriceWithItemsHavingBuyingPrice(collect($products));

        return [
            'total_items' => $this->total_items,
            'total_buying_price' => $total_buying_price,
            'items_with_buying_price' => $items_with_buying_price,
            'products' => $products,
            'deleted_products' => $request->deleted_products,
        ];
    }

    private function getTotalBuyingPriceWithItemsHavingBuyingPrice($products)
    {
        $items_with_buying_price = 0;
        $total_buying_price = $products->sum(function ($each_product) use (&$items_with_buying_price){
            $each_product_total_buying_cost = 0;
            if (!array_key_exists('combination', $each_product['combinations'])){
                foreach ($each_product['combinations'] as $each_combination){
                    if (array_key_exists( 'channel_data', $each_combination)){
                        $each_product_total_buying_cost += $each_combination['stock'] * $each_combination['channel_data'][0]['cost'];
                        $items_with_buying_price++;
                    }
                }

            } else {
                if (array_key_exists( 'channel_data', $each_product['combinations'])){
                    $each_product_total_buying_cost += $each_product['combinations']['stock'] * $each_product['combinations']['channel_data'][0]['cost'];
                    $items_with_buying_price++;
                }
            }
            return $each_product_total_buying_cost;
        });

        return [
            $total_buying_price,
            $items_with_buying_price,
        ];
    }
}

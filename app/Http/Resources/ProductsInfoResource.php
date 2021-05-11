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
        list($total_buying_price, $items_with_buying_price, $total_items) = $this->getProductsInfo(collect($products));
        return [
            'total_items' => $total_items,
            'total_buying_price' => $total_buying_price,
            'items_with_buying_price' => $items_with_buying_price,
            'products' => $products,
            'deleted_products' => $this->deleted_products,
        ];
    }

    /**
     * @param $products
     * @return array
     */
    private function getProductsInfo($products):array
    {
        $items_with_buying_price = 0;
        $total_items = 0;
        $total_buying_price = $products->sum(function ($each_product) use (&$items_with_buying_price, &$total_items){
            $each_product_total_buying_cost = 0;
            if (!array_key_exists('combination', $each_product['combinations'])){
                foreach ($each_product['combinations'] as $each_combination){
                    $total_items++;
                    if (array_key_exists( 'channel_data', $each_combination)){
                        $each_product_total_buying_cost += $each_combination['stock'] * $each_combination['channel_data'][0]['purchase_price'];
                        $items_with_buying_price++;
                    }
                }

            } else {
                $total_items++;
                if (array_key_exists( 'channel_data', $each_product['combinations'])){
                    $each_product_total_buying_cost += $each_product['combinations']['stock'] * $each_product['combinations']['channel_data'][0]['purchase_price'];
                    $items_with_buying_price++;
                }
            }
            return $each_product_total_buying_cost;
        });

        return [
            $total_buying_price,
            $items_with_buying_price,
            $total_items,
        ];
    }
}

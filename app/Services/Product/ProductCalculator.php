<?php namespace App\Services\Product;

use App\Models\Discount;
use App\Services\Webstore\PosServerClient;
use App\Interfaces\SkuChannelRepositoryInterface;
use App\Models\Product;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ProductCalculator
{
    private Product $product;
    private SkuChannelRepositoryInterface $skuChannelRepositoryInterface;
    private $channel;

    public function __construct(SkuChannelRepositoryInterface $skuChannelRepositoryInterface, PosServerClient $client)
    {
        $this->skuChannelRepositoryInterface = $skuChannelRepositoryInterface;
        $this->client = $client;
    }

    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    public function setChannel($channel)
    {
        $this->channel = $channel;
        return $this;
    }

    public function skuChannelWithMinimumPrice()
    {
        $sku_ids = $this->product->skus->pluck('id')->toArray();
        $q = $this->skuChannelRepositoryInterface->whereIn('sku_id', $sku_ids)->where('channel_id', $this->channel);
        return $q->where('price', $q->min('price'))->first();
    }

    public function getOriginalPrice()
    {
       return  $this->skuChannelRepositoryInterface->builder()->where('channel_id', $this->channel)->wherehas('sku',function ($q){
           $q->where('product_id',$this->product->id);
       })->get()->min('price');
    }

    public function getDiscountedPrice()
    {
        $discount = $this->skuChannelWithMinimumPrice()->validDiscounts()->orderBy('created_at', 'desc')->first();
        $discount_amount = $discount ? $discount->amount : 0;
        $original_price = $this->getOriginalPrice();
        return [$original_price - $discount_amount,round(($discount_amount/$original_price)*100,2)];
    }


    public function getProductRatingReview($product)
    {
        try {
            $client = new Client();
            $request = $client->get('https://pos-order.dev-sheba.xyz/api/v1/products/' . $product->id . '/reviews');
            $response = json_decode($request->getBody()->getContents(), true);
            $rating = array_column($response['reviews'], 'rating');
            $count_rating = count($rating);
            $sum_rating = array_sum($rating);
            $average_rating = $sum_rating / $count_rating;
            return [$average_rating, $count_rating];
        } catch (GuzzleException $exception) {
        }
    }

}

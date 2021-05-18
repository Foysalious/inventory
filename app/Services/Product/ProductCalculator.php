<?php namespace App\Services\Product;


use App\Interfaces\SkuChannelRepositoryInterface;
use App\Models\Product;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ProductCalculator
{
    private Product $product;
    private SkuChannelRepositoryInterface $skuChannelRepositoryInterface;
    private $channel;

    public function __construct(SkuChannelRepositoryInterface $skuChannelRepositoryInterface)
    {
        $this->skuChannelRepositoryInterface = $skuChannelRepositoryInterface;
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

    public function getOriginalPrice()
    {
        $sku_ids = $this->product->skus->pluck('id')->toArray();
        return $this->skuChannelRepositoryInterface->whereIn('sku_id', $sku_ids)->where('channel_id', $this->channel)->min('price');
    }


    public function getProductRatingReview($product)
    {


        try {

            $client = new Client();

            $request = $client->get('https://pos-order.dev-sheba.xyz/api/v1/products/' . $product->partner_id . '/reviews');
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

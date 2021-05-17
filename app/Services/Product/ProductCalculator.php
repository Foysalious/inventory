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


    public function getProductRatingReview()
    {

        // $response = Http::get('https://pos-order.dev-sheba.xyz/api/v1/products/15/reviews?rating=4&order_by=desc');
        try {
            // $response = Http::get('https://api-smanager-webstore.dev-sheba.xyz/api/v1/product/15/reviews?rating=4&order_by=desc');
            $client = new Client();
            $request = $client->get('https://api-smanager-webstore.dev-sheba.xyz/api/v1/product/15/reviews');
            $response = json_decode($request->getBody()->getContents(), true);

            $phones = array_column($response['reviews'], 'rating');
            dd($phones);
//            return $response;
        } catch (GuzzleException $exception) {
            dd($exception);
        }


    }

}

<?php namespace App\Services\Product;


use App\Interfaces\SkuChannelRepositoryInterface;
use App\Models\Channel;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Sku;
use App\Models\SkuChannel;
use App\Services\BaseService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class PriceCalculation extends BaseService
{
    private Product $product;
    private Sku $sku;
    private SkuChannelRepositoryInterface $skuChannelRepositoryInterface;
    private SkuChannel $skuChannel;
    private ?Discount $discount = null;
    private $channel;

    /**
     * PriceCalculation constructor.
     * @param SkuChannelRepositoryInterface $skuChannelRepositoryInterface
     */
    public function __construct(SkuChannelRepositoryInterface $skuChannelRepositoryInterface)
    {
        $this->skuChannelRepositoryInterface = $skuChannelRepositoryInterface;
    }

    /**
     * @param Product $product
     * @return PriceCalculation
     */
    public function setProduct(Product $product): PriceCalculation
    {
        $this->product = $product;
        return $this;
    }


    /**
     * @param Sku $sku
     * @return PriceCalculation
     */
    public function setSku(Sku $sku): PriceCalculation
    {
        $this->sku = $sku;
        return $this;
    }
    public function setChannel(Channel $channel): PriceCalculation
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * @param SkuChannel $skuChannel
     * @return PriceCalculation
     */
    public function setSkuChannel(SkuChannel $skuChannel): PriceCalculation
    {
        $this->skuChannel = $skuChannel;
        return $this;
    }

    private function calculateSkuChannel()
    {
        if ($this->skuChannel instanceof SkuChannel) return;
        $this->skuChannel = $this->skuChannelRepositoryInterface
            ->where('sku_id', $this->sku->id)
            ->where('channel_id', $this->channel)
            ->first();
    }

    public function getOriginalUnitPrice()
    {
        $this->calculateSkuChannel();
        return $this->skuChannel->price;
    }

    public function getOriginalUnitPriceWithVat()
    {
        return $this->skuChannel->price + ($this->skuChannel->price * $this->getVatPercentage()) / 100;
    }

    public function getVatPercentage()
    {
        $this->calculateSkuChannel();
        return $this->product->vat_percentage;
    }

    public function getPurchaseUnitPrice()
    {
        $this->calculateSkuChannel();
        return $this->skuChannel->cost;
    }

    public function getWholesalePrice()
    {
        $this->calculateSkuChannel();
        return $this->skuChannel->wholesale_price;
    }

    public function getWholesalePriceWithVat()
    {
        return $this->skuChannel->wholesale_price + (($this->skuChannel->wholesale_price * $this->getVatPercentage()) / 100);
    }

    public function calculateDiscount()
    {
        if ($this->discount instanceof Discount) return;
        $this->calculateSkuChannel();
        $this->discount = $this->skuChannel->validDiscounts->sortByDesc('created_at')->first();
    }

    public function getDiscountAmount()
    {
        $this->calculateDiscount();
        return $this->discount ? $this->discount->amount : 0;
    }

    public function isDiscountPercentage()
    {
        $this->calculateDiscount();
        return $this->discount ? $this->discount->is_amount_percentage : 0;
    }

    public function getDiscountedUnitPrice()
    {
        $this->calculateDiscount();
        if (!$this->discount) return $this->getOriginalUnitPrice();
        if (!$this->discount->is_amount_percentage)
            return $this->getOriginalUnitPrice() - $this->discount->amount;
        return $this->getOriginalUnitPrice() - (($this->getOriginalUnitPrice() * $this->discount->amount) / 100);
    }

    public function getWebstoreOriginalPrice()
    {
        return  $this->skuChannelRepositoryInterface->builder()->where('channel_id', $this->channel)->wherehas('sku',function ($q){
            $q->where('product_id',$this->product->id);
        })->get()->min('price');
    }
    public function skuChannelWithMinimumPrice()
    {
        $sku_ids = $this->product->skus->pluck('id')->toArray();
        $q = $this->skuChannelRepositoryInterface->whereIn('sku_id', $sku_ids)->where('channel_id', $this->channel);
        return $q->where('price', $q->min('price'))->first();
    }
    public function getWebstoreDiscountedPrice()
    {
        $channel_discount = $this->skuChannelWithMinimumPrice() ? $this->skuChannelWithMinimumPrice()->validDiscounts()->orderBy('created_at', 'desc')->first() : null;
        $discount_amount = $channel_discount ? $channel_discount->amount : 0;
        $original_price = $this->getWebstoreOriginalPrice();
        if(!$original_price)
            dd($original_price);
        return [$original_price - $discount_amount,round(($discount_amount/$original_price)*100,2)];
    }

    /**
     * @throws GuzzleException
     */
    public function getProductRatingReview($product)
    {
        try {
            $client = new Client();
            $request = $client->get('https://pos-order.dev-sheba.xyz/api/v1/products/' . $product->id . '/reviews');
            $response = json_decode($request->getBody()->getContents(), true);
            $rating = array_column($response['reviews'], 'rating');
            $count_rating = count($rating);
            $sum_rating = array_sum($rating);
            $average_rating = round($sum_rating / $count_rating);
            return [$average_rating, $count_rating];
        } catch (GuzzleException $exception) {
            throw $exception;
        }
    }
    public function getOriginalPrice()
    {
        return  $this->skuChannelRepositoryInterface->builder()->where('channel_id', $this->channel)->wherehas('sku',function ($q){
            $q->where('product_id',$this->product->id);
        })->get()->min('price');
    }

}

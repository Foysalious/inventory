<?php namespace App\Services\Product;


use App\Interfaces\SkuChannelRepositoryInterface;
use App\Models\Channel;
use App\Models\Discount;
use App\Models\Sku;
use App\Models\SkuChannel;
use App\Services\BaseService;

class PriceCalculation extends BaseService
{
    private Sku $sku;
    private Channel $channel;
    private SkuChannelRepositoryInterface $skuChannelRepositoryInterface;
    private SkuChannel $skuChannel;
    private $discount;

    /**
     * PriceCalculation constructor.
     * @param SkuChannelRepositoryInterface $skuChannelRepositoryInterface
     */
    public function __construct(SkuChannelRepositoryInterface $skuChannelRepositoryInterface)
    {
        $this->skuChannelRepositoryInterface = $skuChannelRepositoryInterface;
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

    /**
     * @param Channel $channel
     * @return PriceCalculation
     */
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
        if (!$this->skuChannel instanceof SkuChannel) {
            $this->skuChannel = $this->skuChannelRepositoryInterface
                ->where('sku_id', $this->sku->id)
                ->where('channel_id', $this->channel)
                ->first();
        }
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
        return $this->skuChannel->sku->product->vat_percentage;
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
        $this->calculateSkuChannel();
        if (!$this->discount instanceof Discount) {
            $this->discount = $this->skuChannel::validDiscounts()->orderBy('created_at', 'desc')->first();
        }
    }

    public function getDiscountAmount()
    {
        $this->calculateDiscount();
        return $this->discount ? $this->discount->amount : 0;
    }

    public function isDiscountPercentage()
    {
        $this->calculateDiscount();
        return $this->discount ? $this->discount->is_discount_percentage : 0;
    }

    public function getDiscountedUnitPrice()
    {
        $this->calculateDiscount();
        if (!$this->discount) return $this->getOriginalUnitPriceWithVat();
        if (!$this->discount->is_amount_percentage)
            return $this->getOriginalUnitPriceWithVat() - $this->discount->amount;
        return $this->getOriginalUnitPriceWithVat() - (($this->getOriginalUnitPriceWithVat() * $this->discount->amount) / 100);
    }

}

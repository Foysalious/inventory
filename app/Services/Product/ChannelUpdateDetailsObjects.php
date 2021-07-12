<?php namespace App\Services\Product;

use App\Exceptions\ProductDetailsPropertyValidationError;

class ChannelUpdateDetailsObjects
{

    private $channelDetails;
    private $price;
    private $wholeSalePrice;
    private $channelId;
    private $skuChannelId;
    private $isPercentage;
    private $discount;
    private $discount_end_date;
    private $discount_details;

    /**
     * @return mixed
     */
    public function getDiscountEndDate()
    {
        return $this->discount_end_date;
    }

    /**
     * @return mixed
     */
    public function getDiscountDetails()
    {
        return $this->discount_details;
    }

    /**
     * @param mixed $channelDetails
     */
    public function setChannelDetails($channelDetails)
    {
        $this->channelDetails = $channelDetails;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getWholeSalePrice()
    {
        return $this->wholeSalePrice;
    }

    /**
     * @return mixed
     */
    public function getIsPercentage()
    {
        return $this->isPercentage;
    }

    public function getDiscount()
    {
        return $this->discount;
    }

    public function getChannelId()
    {
        return $this->channelId;
    }

    public function getSkuChannelId()
    {
        return $this->skuChannelId;
    }

    /**
     * @return $this
     * @throws ProductDetailsPropertyValidationError
     */
    public function build()
    {
        if(!$this->validate())
            throw new ProductDetailsPropertyValidationError();
        $this->price = $this->channelDetails->price;
        $this->wholeSalePrice = $this->channelDetails->wholesale_price;
        $this->channelId = $this->channelDetails->channel_id;
        $this->skuChannelId = $this->channelDetails->sku_channel_id;
        $this->isPercentage = $this->channelDetails->is_percentage ?? null;
        $this->discount = $this->channelDetails->discount ?? null;
        $this->discount_end_date = $this->channelDetails->discount_end_date ?? null;
        $this->discount_details = $this->channelDetails->discount_details ?? '';
        return $this;
    }


    public function validate()
    {
        return property_exists( $this->channelDetails,'channel_id')
            && (property_exists( $this->channelDetails,'price')) && (property_exists( $this->channelDetails,'wholesale_price'))
            && (property_exists( $this->channelDetails,'sku_channel_id'));
    }


}

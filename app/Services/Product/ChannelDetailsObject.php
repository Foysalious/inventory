<?php namespace App\Services\Product;


use App\Exceptions\ProductDetailsPropertyValidationError;

class ChannelDetailsObject
{
    protected $discountEndDate;
    private $channelDetails;
    private $price;
    private $cost;
    private $wholeSalePrice;
    private $channelId;
    private $isPercentage;
    private $discount;
    private $details;

    /**
     * @param mixed $details
     * @return ChannelDetailsObject
     */
    public function setDetails($details)
    {
        $this->details = $details;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param mixed $discountEndDate
     * @return ChannelDetailsObject
     */
    public function setDiscountEndDate($discountEndDate)
    {
        $this->discountEndDate = $discountEndDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDiscountEndDate()
    {
        return $this->discountEndDate;
    }

    /**
     * @param mixed $isPercentage
     * @return ProductDetailsObject
     */
    public function setIsPercentage($isPercentage)
    {
        $this->isPercentage = $isPercentage;
        return $this;
    }

    /**
     * @param mixed $discount
     * @return ProductDetailsObject
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsPercentage()
    {
        return $this->isPercentage;
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
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
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @return mixed
     */
    public function getWholeSalePrice()
    {
        return $this->wholeSalePrice;
    }

    /**
     * @return $this
     * @throws ProductDetailsPropertyValidationError
     */
    public function build()
    {
        if(!$this->validate())
            throw new ProductDetailsPropertyValidationError();
        $this->cost = $this->channelDetails->cost;
        $this->price = $this->channelDetails->price;
        $this->wholeSalePrice = $this->channelDetails->wholesale_price;
        $this->channelId = $this->channelDetails->channel_id;
        $this->details = $this->channelDetails->details;
        $this->isPercentage = $this->channelDetails->is_percentage;
        $this->discount = $this->channelDetails->discount;
        $this->discountEndDate = $this->channelDetails->discount_end_date;
        return $this;

    }

    public function getChannelId()
    {
        return $this->channelId;
    }

    public function validate()
    {
        return (property_exists( $this->channelDetails,'channel_id')) && (property_exists( $this->channelDetails,'cost'))
        && (property_exists( $this->channelDetails,'price')) && (property_exists( $this->channelDetails,'wholesale_price'));
    }

}

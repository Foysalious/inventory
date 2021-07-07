<?php namespace App\Services\Product;


use App\Exceptions\ProductDetailsPropertyValidationError;

class ChannelDetailsObject
{
    protected $discountEndDate;
    private $channelDetails;
    private $price;
    private $wholeSalePrice;
    private $channelId;
    private $isPercentage;
    private $discount;
    private $discount_details;


    /**
     * @param $discount_details
     * @return $this
     */
    public function setDiscountDetails($discount_details)
    {
        $this->discount_details = $discount_details;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDiscountDetails()
    {
        return $this->discount_details;
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
     * @param $isPercentage
     * @return $this
     */
    public function setIsPercentage($isPercentage)
    {
        $this->isPercentage = $isPercentage;
        return $this;
    }

    /**
     * @param $discount
     * @return $this
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
        $this->price = $this->channelDetails->price;
        $this->wholeSalePrice = $this->channelDetails->wholesale_price;
        $this->channelId = $this->channelDetails->channel_id;
        $this->discount_details = $this->channelDetails->discount_details ?? '';
        $this->isPercentage = $this->channelDetails->is_percentage ?? null;
        $this->discount = $this->channelDetails->discount ?? null;
        $this->discountEndDate = $this->channelDetails->discount_end_date ?? null;
        return $this;

    }

    public function getChannelId()
    {
        return $this->channelId;
    }

    public function validate()
    {
        return (property_exists( $this->channelDetails,'channel_id'))
            && (property_exists( $this->channelDetails,'price'))
            && (property_exists( $this->channelDetails,'wholesale_price'));
    }

}

<?php namespace App\Services\Product;

use App\Exceptions\ProductDetailsPropertyValidationError;

class ProductUpdateDetailsObjects
{
    private $productDetail;
    private $combination;
    private $stock;
    private $channelData;
    private $hasVariant;
    private ?float $weight;
    private ?string $weightUnit;

    public function setProductDetail($productDetail)
    {
        $this->productDetail =  $productDetail;
        return $this;
    }

    public function hasVariant($hasValiant)
    {
        $this->hasVariant = $hasValiant;
        return $this;
    }

    public function build()
    {
        if(!$this->validate())
            throw new ProductDetailsPropertyValidationError();
        if($this->hasVariant)
            $this->setCombination();
        $this->setStock();
        $this->setWeight();
        $this->setWeightUnit();
        $this->setChannelData();
        return $this;
    }

    /**
     * @return bool|mixed
     */
    public function validate()
    {
        return  (property_exists($this->productDetail,'combination') && property_exists($this->productDetail,'stock')
            && property_exists($this->productDetail,'channel_data'));
    }


    public function setCombination()
    {

        $final = [];
        foreach ($this->productDetail->combination as $option_value) {
            array_push($final, app(CombinationUpdateDetailsObject::class)->setCombinationDetail($option_value)->build());
        }
        $this->combination = $final;
        return $this;
    }

    public function getCombination()
    {
        return $this->combination;
    }

    public function setStock()
    {
        $this->stock = $this->productDetail->stock;
        return $this;
    }

    public function setWeight()
    {
        $this->weight = $this->productDetail->weight ?? null;
        return $this;
    }

    public function setWeightUnit()
    {
        $this->weightUnit = $this->productDetail->weight_unit ?? null;
        return $this;
    }

    public function getStock()
    {
        return $this->stock;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function getWeightUnit()
    {
        return $this->weightUnit;
    }

    public function setChannelData()
    {
        $final = [];
        foreach ($this->productDetail->channel_data as $channel_data) {
            array_push($final, app(ChannelUpdateDetailsObjects::class)->setChannelDetails($channel_data)->build());
        }
        $this->channelData = $final;
        return $this;

    }
    public function getChannelData()
    {
        return $this->channelData;
    }

}

<?php namespace App\Services\Product;


class ProductDetailsObject
{
    private $productDetail;
    private  $combination;
    private $stock;
    private $channelData;

    public function setProductDetail($productDetail)
    {
        $this->productDetail =  $productDetail;
        return $this;
    }

    public function build()
    {
        $this->validate();
        $this->setCombination();
        $this->setStock();
        $this->setChannelData();
        return $this;
    }


    public function validate()
    {
        return (property_exists( $this->productDetail,['combination','stock','channel_data']))?: throw new ProductDetailsPropertyValidationError();
    }


    public function setCombination()
    {

        $final = [];
        foreach ($this->combination as $option_value) {
            array_push($final, app(CombinationDetailsObject::class)->setCombinationDetail($option_value))->build();
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

    public function getStock()
    {
        return $this->stock;
    }
    public function setChannelData()
    {


        $final = [];
        foreach ($this->channelData as $channel_data) {
            array_push($final, app(ChannelDetailsObject::class)->setChannelDetail($channel_data))->build();
        }

        $this->channelData = $final;
        return $this;

    }

    public function getChannelData()
    {
        return $this->channelData;
    }

}

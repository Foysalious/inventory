<?php namespace App\Services\Product;


class ProductDetailsObject
{
    private $productDetail;
    private $combination;
    private $stock;
    private $channelData;

    public function setProductDetail($productDetail)
    {
        $this->productDetail =  $productDetail;
        return $this;
    }



    public function build()
    {
        $this->setCombination();
        $this->setStock();
        $this->setChannelData();
        return $this;
    }

    public function setCombination()
    {
         $this->combination = $this->productDetail->combination;
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
        $this->channelData = $this->productDetail->channel_data;
        return $this;
    }

    public function getChannelData()
    {
        return $this->channelData;
    }







}

<?php namespace App\Services\Product;

use App\Exceptions\ProductDetailsPropertyValidationError;

class ProductUpdateDetailsObjects
{
    private $productDetail;
    /** @var CombinationUpdateDetailsObject[] */
    private array $combination;
    private ?float $stock;
    private ?float $cost;
    private array $channelData;
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
        $this->setCost();
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

    /**
     * @return CombinationUpdateDetailsObject[]
     */
    public function getCombination(): array
    {
        return $this->combination;
    }

    public function setStock()
    {
        $this->stock = $this->productDetail->stock;
        return $this;
    }

    /**
     * @return $this
     */
    public function setCost()
    {
        $this->cost = $this->productDetail->cost;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
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
            /** @var ChannelUpdateDetailsObjects $channel_obj */
            $channel_obj = app(ChannelUpdateDetailsObjects::class);
            array_push($final, $channel_obj->setChannelDetails($channel_data)->build());
        }
        $this->channelData = $final;
        return $this;

    }

    /**
     * @return ChannelUpdateDetailsObjects[]
     */
    public function getChannelData(): array
    {
        return $this->channelData;
    }

}

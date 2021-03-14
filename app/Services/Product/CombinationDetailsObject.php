<?php namespace App\Services\Product;


use App\Exceptions\ProductDetailsPropertyValidationError;

class CombinationDetailsObject
{
    private $combinationDetail;
    private $option;
    private $value;

    public function setCombinationDetail($combinationDetail)
    {
        $this->combinationDetail =  $combinationDetail;
        return $this;
    }

    public function build()
    {
        if(!$this->validate())
            throw new ProductDetailsPropertyValidationError();
        $this->option = $this->combinationDetail->option;
        $this->value = $this->combinationDetail->value;
        return $this;

    }

    public function validate()
    {
        return (property_exists( $this->combinationDetail,'option')) && (property_exists( $this->combinationDetail,'value'));
    }


    public function getOption()
    {
        return $this->option;
    }

    public function getValue()
    {
        return $this->value;
    }




}

<?php namespace App\Services\Product;


use App\Exceptions\ProductDetailsPropertyValidationError;

class CombinationDetailsObject
{
    private $combinationDetail;
    private $option;
    private $value;
    private $value_details;

    public function setCombinationDetail($combinationDetail)
    {
        $this->combinationDetail =  $combinationDetail;
        return $this;
    }

    public function build()
    {
        if(!$this->validate())
            throw new ProductDetailsPropertyValidationError();
        $this->option = $this->combinationDetail->option_name;
        $this->value = $this->combinationDetail->option_value_name;
        $this->value_details = $this->combinationDetail->option_value_details;
        return $this;

    }

    public function validate()
    {
        return (property_exists( $this->combinationDetail,'option_name'))
            && (property_exists( $this->combinationDetail,'option_value_name')
            && property_exists( $this->combinationDetail,'option_value_details'));
    }


    public function getOption()
    {
        return $this->option;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getValueDetails()
    {
        return $this->value_details;
    }
}

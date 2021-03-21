<?php namespace App\Services\Product;


use App\Exceptions\ProductDetailsPropertyValidationError;

class UpdatedCombinationDetailsObject
{
    private $combinationDetail;
    private $optionId;
    private $optionName;
    private $optionValueName;
    private $optionValueId;

    public function setCombinationDetail($combinationDetail)
    {
        $this->combinationDetail =  $combinationDetail;
        return $this;
    }

    public function build()
    {
        if (!$this->validate())
            throw new ProductDetailsPropertyValidationError();
        $this->optionId = $this->combinationDetail->option_id;
        $this->optionName = $this->combinationDetail->option_name;
        $this->optionValueId = $this->combinationDetail->option_value_id;
        $this->optionValueName = $this->combinationDetail->option_value_name;
        return $this;
    }

    public function validate()
    {
        return (property_exists( $this->combinationDetail,'option_id')) && (property_exists( $this->combinationDetail,'option_name'))
            && (property_exists( $this->combinationDetail,'option_value_id'))  && (property_exists( $this->combinationDetail,'option_value_name'));
    }


    public function getOptionId()
    {
        return $this->optionId;
    }

    public function getOptionName()
    {
        return $this->optionName;
    }

    public function getOptionValueId()
    {
        return $this->optionValueId;
    }

    public function getOptionValueName()
    {
        return $this->optionValueName;
    }

}

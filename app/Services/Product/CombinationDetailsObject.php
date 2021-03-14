<?php namespace App\Services\Product;


class CombinationDetailsObject
{
    private $combinationDetail;
    private $option;
    private $value;

    public function setCombinationDetail($combinationDetail)
    {
        $this->combinationDetail =  $combinationDetail;
    }

    public function build()
    {
        $this->validate();
        $this->option = $this->combinationDetail->option;
        $this->value = $this->combinationDetail->value;
        return $this;

    }

    public function validate()
    {
        return (property_exists( $this->combinationDetail,['option','value']))?: throw new CombinationDetailsPropertyValidationError();
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

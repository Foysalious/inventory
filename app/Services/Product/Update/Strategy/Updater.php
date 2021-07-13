<?php namespace App\Services\Product\Update\Strategy;


use App\Models\Product;
use App\Services\Product\Update\Strategy\NonVariant\NonVariant;
use App\Services\Product\Update\Strategy\Variant\OptionsAdd;
use App\Services\Product\Update\Strategy\Variant\OptionsDelete;
use App\Services\Product\Update\Strategy\Variant\OptionsUpdate;
use App\Services\Product\Update\Strategy\Variant\ValuesAdd;
use App\Services\Product\Update\Strategy\Variant\ValuesDelete;
use App\Services\Product\Update\Strategy\Variant\ValuesUpdate;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class Updater
{
    protected NonVariant|OptionsUpdate|OptionsDelete|ValuesAdd|OptionsAdd|ValuesDelete|ValuesUpdate $strategy;
    protected Product $product;
    protected $updateDataObjects;
    private $deletedValues;

    /**
     * @param NonVariant|OptionsAdd|OptionsDelete|OptionsUpdate|ValuesAdd|ValuesDelete|ValuesUpdate $strategy
     * @return $this
     */
    public function setStrategy(ValuesDelete|ValuesUpdate|NonVariant|OptionsAdd|ValuesAdd|OptionsUpdate|OptionsDelete $strategy): Updater
    {
        $this->strategy = $strategy;
        return $this;
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    public function setUpdatedDataObjects($updateDataObjects)
    {
        $this->updateDataObjects = $updateDataObjects;
        return $this;
    }

    public function setDeletedValues($deletedValues)
    {
        $this->deletedValues = $deletedValues;
        return $this;
    }

    /**
     * @throws UnknownProperties
     */
    public function update()
    {
        $this->strategy->setProduct($this->product)->setUpdatedDataObjects($this->updateDataObjects)->setDeletedValues($this->deletedValues)->update();
    }
}

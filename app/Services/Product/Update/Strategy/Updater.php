<?php namespace App\Services\Product\Update\Strategy;


use App\Models\Product;
use App\Services\Product\ProductUpdateDetailsObjects;

class Updater
{
    protected ProductUpdateStrategy $strategy;
    protected Product $product;
    /** @var ProductUpdateDetailsObjects[] */
    protected array $updateDataObjects;
    private ?array $deletedValues;
    protected bool $hasVariant;

    /**
     * @param ProductUpdateStrategy $strategy
     * @return $this
     */
    public function setStrategy(ProductUpdateStrategy $strategy): Updater
    {
        $this->strategy = $strategy;
        return $this;
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product): Updater
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @param bool $hasVariant
     * @return Updater
     */
    public function setHasVariant(bool $hasVariant): Updater
    {
        $this->hasVariant = $hasVariant;
        return $this;
    }

    /**
     * @param ProductUpdateDetailsObjects[] $updateDataObjects
     * @return $this
     */
    public function setUpdatedDataObjects(array $updateDataObjects): Updater
    {
        $this->updateDataObjects = $updateDataObjects;
        return $this;
    }

    /**
     * @param array|null $deletedValues
     * @return $this
     */
    public function setDeletedValues(?array $deletedValues): Updater
    {
        $this->deletedValues = $deletedValues;
        return $this;
    }

    public function update()
    {
        $this->strategy->setProduct($this->product)->setHasVariant($this->hasVariant)->setUpdatedDataObjects($this->updateDataObjects)->setDeletedValues($this->deletedValues)->update();
    }
}

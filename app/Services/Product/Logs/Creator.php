<?php namespace App\Services\Product\Logs;


use App\Interfaces\ProductUpdateLogRepositoryInterface;
use App\Models\Product;

class Creator
{
    private $product;
    private $fieldNames;
    private $oldValue;
    private $newValue;
    private $productUpdateLogRepositoryInterface;

    /**
     * Creator constructor.
     * @param ProductUpdateLogRepositoryInterface $productUpdateLogRepositoryInterface
     */
    public function __construct(ProductUpdateLogRepositoryInterface $productUpdateLogRepositoryInterface)
    {
        $this->productUpdateLogRepositoryInterface = $productUpdateLogRepositoryInterface;
    }

    /**
     * @param Product $product
     * @return Creator
     */

    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @param mixed $fieldNames
     * @return Creator
     */
    public function setFieldNames($fieldNames)
    {
        $this->fieldNames = $fieldNames;
        return $this;
    }

    /**
     * @param mixed $oldValue
     * @return Creator
     */
    public function setOldValue($oldValue)
    {
        $this->oldValue = $oldValue;
        return $this;
    }

    /**
     * @param mixed $newValue
     * @return Creator
     */
    public function setNewValue($newValue)
    {
        $this->newValue = $newValue;
        return $this;
    }

    public function create()
    {
        $this->productUpdateLogRepositoryInterface->create($this->makeData());
    }

    private function makeData()
    {
        return [
            'product_id' => $this->product->id,
            'field_names' => json_encode($this->fieldNames),
            'old_value' => json_encode($this->oldValue),
            'new_value' => json_encode($this->newValue)
        ];
    }

}

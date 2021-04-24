<?php namespace App\Services\Product\Logs;


use App\Http\Resources\ProductResource;
use App\Services\Product\CombinationService;
use App\Services\Product\Logs\Creator as ProductUpdateLogsCreator;

class ProductUpdateLogCreateRequest
{
    private $oldProductDetails;
    private $updatedProductDetails;
    /** @var CombinationService */
    protected $combinationService;
    /**
     * @var Creator
     */
    private Creator $logCreator;

    /**
     * ProductUpdateLogCreateRequest constructor.
     * @param CombinationService $combinationService
     * @param Creator $logCreator
     */
    public function __construct(CombinationService $combinationService, ProductUpdateLogsCreator $logCreator)
    {
        $this->combinationService = $combinationService;
        $this->logCreator = $logCreator;
    }

    /**
     * @param mixed $oldProductDetails
     * @return ProductUpdateLogCreateRequest
     */
    public function setOldProductDetails($oldProductDetails)
    {
        $this->oldProductDetails = $oldProductDetails;
        return $this;
    }

    /**
     * @param mixed $updatedProductDetails
     * @return ProductUpdateLogCreateRequest
     */
    public function setUpdatedProductDetails($updatedProductDetails)
    {
        $this->updatedProductDetails = $updatedProductDetails;
        return $this;
    }

    public function create()
    {
        $oldProduct = $this->getProductObject($this->oldProductDetails);
        $updatedProduct = $this->getProductObject($this->updatedProductDetails);
        $fieldNames = $this->getUpdatedFieldNames($this->oldProductDetails, $this->updatedProductDetails);
        $this->logCreator->setProduct($this->updatedProductDetails)
            ->setFieldNames($fieldNames)
            ->setOldValue($oldProduct)
            ->setNewValue($updatedProduct)
            ->create();
    }

    private function getProductObject($product)
    {
        list($options,$combinations) = $this->combinationService->getCombinationData($product);
        $product->options = collect($options);
        $product->combinations = collect($combinations);
        return new ProductResource($product);
    }

    private function getUpdatedFieldNames($oldProductDetails, $updatedProductDetails)
    {
        $diff = array_diff($oldProductDetails->toArray(), $updatedProductDetails->toArray());
        unset($diff['updated_at']);
        unset($diff['updated_by_name']);
        return array_keys($diff);
    }


}

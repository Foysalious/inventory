<?php namespace App\Services\Product\Logs;


use App\Http\Resources\WebstoreProductResource;
use App\Services\Product\CombinationService;
use App\Services\Product\Logs\Creator as ProductUpdateLogsCreator;

class ProductUpdateLogCreateRequest
{
    private $oldProductDetails;
    private $updatedProductDetails;
    /** @var CombinationService */
    protected $combinationService;
    protected $oldProductResource;
    protected $updatedProductResource;
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

    /**
     * @param mixed $oldProductResource
     * @return ProductUpdateLogCreateRequest
     */
    public function setOldProductResource($oldProductResource)
    {
        $this->oldProductResource = $oldProductResource;
        return $this;
    }

    /**
     * @param mixed $updatedProductResource
     * @return ProductUpdateLogCreateRequest
     */
    public function setUpdatedProductDetailsResource($updatedProductResource)
    {
        $this->updatedProductResource = $updatedProductResource;
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
        return new WebstoreProductResource($product);
    }

    private function getUpdatedFieldNames($oldProductDetails, $updatedProductDetails)
    {
        $oldProductArray = collect($oldProductDetails)->toArray();
        $updatedProductArray = collect($updatedProductDetails)->toArray();

        $diff = $this->getOldUpdatedProductDifference($oldProductArray, $updatedProductArray);
        return array_keys($diff);
    }

    private function getOldUpdatedProductDifference($oldProductArray, $updatedProductArray)
    {
        $diff = [];
        if(isset($updatedProductArray['name'])) $oldProductArray['name'] !== $updatedProductArray['name'] ? $diff['name'] = $updatedProductArray['name'] : [];
        if(isset($updatedProductArray['category_id']))  $oldProductArray['category_id'] !== $updatedProductArray['category_id'] ?  $diff['category_id'] = $updatedProductArray['category_id'] : [];
        if(isset($updatedProductArray['sub_category_id'])) $oldProductArray['sub_category_id'] !== $updatedProductArray['sub_category_id'] ? $diff['sub_category_id'] = $updatedProductArray['sub_category_id'] : [];
        if(isset($updatedProductArray['description']))  $oldProductArray['description'] !== $updatedProductArray['description'] ? $diff['description'] = $updatedProductArray['description'] : [];
        if(isset($updatedProductArray['vat_percentage']))   $oldProductArray['vat_percentage'] !== $updatedProductArray['vat_percentage'] ? $diff['vat_percentage'] = $updatedProductArray['vat_percentage'] : [];
        if(isset($updatedProductArray['app_thumb']))    $oldProductArray['app_thumb'] !== $updatedProductArray['app_thumb'] ? $diff['app_thumb'] = $updatedProductArray['app_thumb'] : [];
        if(isset($updatedProductArray['warranty'])) $oldProductArray['warranty'] !== $updatedProductArray['warranty'] ? $diff['warranty'] = $updatedProductArray['warranty'] : [];
        if(isset($updatedProductArray['warranty_unit']))    $oldProductArray['warranty_unit'] !== $updatedProductArray['warranty_unit'] ? $diff['warranty_unit'] = $updatedProductArray['warranty_unit'] : [];
        if(isset($updatedProductArray['original_price']))   $oldProductArray['original_price'] !== $updatedProductArray['original_price'] ? $diff['original_price'] = $updatedProductArray['original_price'] : [];
        if(isset($updatedProductArray['unit']['id'])) $oldProductArray['unit']['id'] !== $updatedProductArray['unit']['id'] ? $diff['unit'] = $updatedProductArray['unit']['id'] : [];
        if(isset($updatedProductArray['unit_id'])) $oldProductArray['unit_id'] !== $updatedProductArray['unit_id'] ? $diff['unit'] = $updatedProductArray['unit_id'] : [];
        return $diff;
    }

}

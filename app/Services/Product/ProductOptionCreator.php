<?php namespace App\Services\Product;


use App\Interfaces\ProductOptionRepositoryInterface;


class ProductOptionCreator
{
    private $productId;
    private $optionName;
    private $productOptionRepository;


    public function __construct(ProductOptionRepositoryInterface $productOptionRepository)
    {
        $this->productOptionRepository = $productOptionRepository;
    }

    /**
     * @param mixed $optionId
     * @return ProductOptionCreator
     */
    public function setOptionName($optionName)
    {
        $this->optionName = $optionName;
        return $this;
    }

    /**
     * @param mixed $productId
     * @return ProductOptionCreator
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }


    private function makeData()
    {
       return [
           'product_id' => $this->productId,
           'name' => $this->optionName
       ];
    }

    public function create()
    {
        return $this->productOptionRepository->firstOrCreate($this->makeData());
    }


}

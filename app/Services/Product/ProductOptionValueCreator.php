<?php namespace App\Services\Product;


use App\Interfaces\ProductOptionValueRepositoryInterface;

class ProductOptionValueCreator
{


    private $productOptionId;
    private $valueName;
    private $productOptionValueRepository;


    public function __construct(ProductOptionValueRepositoryInterface  $productOptionValueRepository)
    {
        $this->productOptionValueRepository = $productOptionValueRepository;
    }


    /**
     * @param $valueName
     * @return $this
     */
    public function setValueName($valueName)
    {
        $this->valueName = $valueName;
        return $this;
    }


    /**
     * @param $productOptionId
     * @return $this
     */
    public function setProductOptionId($productOptionId)
    {
        $this->productOptionId = $productOptionId;
        return $this;
    }

    public function create()
    {
        return $this->productOptionValueRepository->create($this->makeData());
    }


    private function makeData()
    {
        return [
            'product_option_id' => $this->productOptionId,
            'name' => $this->valueName
        ];
    }


}

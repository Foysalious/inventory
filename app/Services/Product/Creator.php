<?php namespace App\Services\Product;


use App\Interfaces\ProductRepositoryInterface;

class Creator
{
    protected ProductRepositoryInterface $productRepositoryInterface;
    protected $partnerId;
    protected $categoryId;
    protected $name;
    protected $description;
    protected $showImage;
    protected $warranty;
    protected $warrantyUnit;
    protected $vatPercentage;
    protected $unitId;

    /**
     * Creator constructor.
     * @param ProductRepositoryInterface $productRepositoryInterface
     */
    public function __construct(ProductRepositoryInterface $productRepositoryInterface)
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
    }


    /**
     * @param mixed $partnerId
     * @return Creator
     */
    public function setPartnerId($partnerId)
    {
        $this->partnerId = $partnerId;
        return $this;
    }

    /**
     * @param mixed $categoryId
     * @return Creator
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    /**
     * @param mixed $name
     * @return Creator
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $description
     * @return Creator
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param mixed $showImage
     * @return Creator
     */
    public function setShowImage($showImage)
    {
        $this->showImage = $showImage;
        return $this;
    }

    /**
     * @param mixed $warranty
     * @return Creator
     */
    public function setWarranty($warranty)
    {
        $this->warranty = $warranty;
        return $this;
    }

    /**
     * @param mixed $warrantyUnit
     * @return Creator
     */
    public function setWarrantyUnit($warrantyUnit)
    {
        $this->warrantyUnit = $warrantyUnit;
        return $this;
    }

    /**
     * @param mixed $vatPercentage
     * @return Creator
     */
    public function setVatPercentage($vatPercentage)
    {
        $this->vatPercentage = $vatPercentage;
        return $this;
    }

    /**
     * @param mixed $unitId
     * @return Creator
     */
    public function setUnitId($unitId)
    {
        $this->unitId = $unitId;
        return $this;
    }

    public function create()
    {
        return $this->productRepositoryInterface->create($this->makeData());
    }

    private function makeData()
    {
        return [
            'partner_id' => $this->partnerId,
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'description' => $this->description,
            'show_image' => $this->showImage ?: 1,
            'warranty' => $this->warranty ?: 0,
            'warranty_unit' => $this->warrantyUnit ?: 'day',
            'vat_percentage' => $this->vatPercentage ?: 0,
            'unit_id' => $this->unitId,
        ];
    }


}

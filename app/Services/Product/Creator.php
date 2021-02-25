<?php namespace App\Services\Product;


use App\Interfaces\DiscountRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Services\Discount\Types;
use Carbon\Carbon;

class Creator
{
    protected ProductRepositoryInterface $productRepositoryInterface;
    protected DiscountRepositoryInterface $discountRepositoryInterface;
    protected $partnerId;
    protected $categoryId;
    protected $name;
    protected $description;
    protected $warranty;
    protected $warrantyUnit;
    protected $vatPercentage;
    protected $unitId;
    protected $discountAmount;
    protected  $discountEndDate;



    /**
     * Creator constructor.
     * @param ProductRepositoryInterface $productRepositoryInterface
     */
    public function __construct(ProductRepositoryInterface $productRepositoryInterface, DiscountRepositoryInterface $discountRepositoryInterface)
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->discountRepositoryInterface = $discountRepositoryInterface;
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

    public function setDiscount($discount_amount)
    {
        $this->discountAmount = $discount_amount;
        return $this;
    }

    public function setDiscountEndDate($discount_end_date)
    {
        $this->discountEndDate = $discount_end_date;
        return $this;
    }

    public function create()
    {
        $product =  $this->productRepositoryInterface->create($this->makeData());
        if($this->discountAmount)
        $this->discountRepositoryInterface->create($this->makeDiscountData($product->id));
        return $product;
    }

    private function makeData()
    {
        return [
            'partner_id' => $this->partnerId,
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'description' => $this->description,
            'warranty' => $this->warranty ?: 0,
            'warranty_unit' => $this->warrantyUnit ?: 'day',
            'vat_percentage' => $this->vatPercentage ?: 0,
            'unit_id' => $this->unitId,
        ];
    }

    private function makeDiscountData($product_id)
    {
       return [
            'type_id' => $product_id,
            'discount_type' => Types::PRODUCT,
            'amount' => $this->discountAmount,
            'start_date' => Carbon::now(),
            'end_date'   => Carbon::parse($this->discountEndDate . ' 23:59:59')
       ];

    }





}

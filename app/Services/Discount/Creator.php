<?php namespace App\Services\Discount;


use App\Interfaces\DiscountRepositoryInterface;
use Carbon\Carbon;

class Creator
{
    protected DiscountRepositoryInterface $discountRepositoryInterface;

    protected $partnerId;
    protected $discountAmount;
    protected $discountEndDate;
    protected $discountType;
    protected $discountTypeId;
    protected $discountDetails;
    protected $isPercentage;

    /**
     * @param mixed $isPercentage
     * @return Creator
     */
    public function setIsPercentage($isPercentage)
    {
        $this->isPercentage = $isPercentage;
        return $this;
    }

    /**
     * @param mixed $discountDetails
     * @return Creator
     */
    public function setDiscountDetails($discountDetails)
    {
        $this->discountDetails = $discountDetails;
        return $this;
    }

    public function __construct(DiscountRepositoryInterface $discountRepositoryInterface)
    {
        $this->discountRepositoryInterface = $discountRepositoryInterface;
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

    /**
     * @param mixed $discountType
     * @return Creator
     */
    public function setDiscountType($discountType)
    {
        $this->discountType = $discountType;
        return $this;
    }

    /**
     * @param mixed $discountTypeId
     * @return Creator
     */
    public function setDiscountTypeId($discountTypeId)
    {
        $this->discountTypeId = $discountTypeId;
        return $this;
    }

    public function create()
    {
        $this->discountRepositoryInterface->create($this->makeData());
    }

    public function createChannelSkuDiscount()
    {
        $this->discountRepositoryInterface->create($this->makeChannelSkuData());
    }

    private function makeChannelSkuData()
    {
        return [
            'type_id'               => $this->discountTypeId,
            'type'                  => $this->discountType,
            'details'               => $this->discountDetails,
            'amount'                => $this->discountAmount,
            'is_amount_percentage'  => $this->isPercentage,
            'cap'                   => null,
            'start_date'            => Carbon::now(),
            'end_date'              => $this->discountEndDate
        ];
    }

    /**
     * @return array
     */
    private function makeData()
    {
        return [
            'type_id' => $this->discountTypeId,
            'discount_type' => $this->discountType,
            'amount' => $this->discountAmount,
            'start_date' => Carbon::now(),
            'end_date'   => Carbon::parse($this->discountEndDate . ' 23:59:59')
        ];
    }

}

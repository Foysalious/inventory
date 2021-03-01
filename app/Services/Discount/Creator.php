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

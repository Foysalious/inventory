<?php namespace App\Repositories;


use App\Interfaces\DiscountRepositoryInterface;
use App\Models\Discount;

class DiscountRepository extends BaseRepository implements DiscountRepositoryInterface
{
    public function __construct(Discount $model)
    {
        parent::__construct($model);
    }


}

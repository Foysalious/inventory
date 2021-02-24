<?php namespace App\Repositories;


use App\Interfaces\PartnerCategoryRepositoryInterface;
use App\Models\PartnerCategory;

class PartnerCategoryRepository extends BaseRepository implements PartnerCategoryRepositoryInterface
{
    public function __construct(PartnerCategory $model)
    {
        parent::__construct($model);
    }
}

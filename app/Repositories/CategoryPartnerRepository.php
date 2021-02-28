<?php namespace App\Repositories;


use App\Interfaces\CategoryPartnerRepositoryInterface;
use App\Models\CategoryPartner;

class CategoryPartnerRepository extends BaseRepository implements CategoryPartnerRepositoryInterface
{
    public function __construct(CategoryPartner $model)
    {
        parent::__construct($model);
    }
}

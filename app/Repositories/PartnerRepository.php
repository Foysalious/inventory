<?php namespace App\Repositories;


use App\Interfaces\PartnerRepositoryInterface;
use App\Models\Partner;

class PartnerRepository extends BaseRepository implements PartnerRepositoryInterface
{
    public function __construct(Partner $model)
    {
        parent::__construct($model);
    }

    public function getProductsInfoByPartner(int $partnerId)
    {
        return $this->model->where('id', $partnerId)->withCount(['products', 'skus', 'batches' => function($q) {
            $q->where('cost', '>', 0);
        }])->withSum('batches', 'cost')->first();
    }
}

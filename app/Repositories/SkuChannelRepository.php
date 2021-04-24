<?php namespace App\Repositories;


use App\Interfaces\SkuChannelRepositoryInterface;
use App\Models\SkuChannel;

class SkuChannelRepository extends BaseRepository implements SkuChannelRepositoryInterface {

    public function __construct(SkuChannel $model)
    {
        parent::__construct($model);
    }

}

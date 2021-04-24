<?php namespace App\Repositories;


use App\Interfaces\ProductChannelRepositoryInterface;
use App\Models\ProductChannel;

class ProductChannelRepository  extends BaseRepository implements ProductChannelRepositoryInterface
{
    public function __construct(ProductChannel $model)
    {
        parent::__construct($model);
    }

}

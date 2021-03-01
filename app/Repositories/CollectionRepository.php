<?php

namespace App\Repositories;

use App\Interfaces\CollectionRepositoryInterface;
use App\Models\Collection;

/**
 * Class CollectionRepository.
 */
class CollectionRepository extends BaseRepository implements CollectionRepositoryInterface
{
    public function __construct(Collection $model)
    {
        parent::__construct($model);
    }

    public function getAllCollection($offset, $limit)
    {
        return $this->model->offset($offset)->limit($limit)->latest()->get();
    }
}

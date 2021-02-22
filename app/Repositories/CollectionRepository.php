<?php

namespace App\Repositories;

use App\Interfaces\CollectionRepositoryInterface;
use App\Models\Collection;

/**
 * Class CollectionRepository.
 */
class CollectionRepository extends BaseRepository implements CollectionRepositoryInterface
{
    /**
     * @param Collection $model
     */

    protected $paginate = 15;

    public function __construct(Collection $model)
    {
        parent::__construct($model);
    }

    public function getAllCollection()
    {
        return $this->model->latest()->paginate($this->paginate);
    }
}

<?php

namespace App\Repositories;

use App\Interfaces\CollectionRepositoryInterface;
use App\Models\Collection;
use Illuminate\Http\Request;

/**
 * Class CollectionRepository.
 */
class CollectionRepository extends BaseRepository implements CollectionRepositoryInterface
{
    public function __construct(Collection $model)
    {
        parent::__construct($model);
    }

    public function getAllCollection(Request $request)
    {
        list($offset, $limit) = calculatePagination($request);
        return $this->model->offset($offset)->limit($limit)->latest()->get();
    }
}

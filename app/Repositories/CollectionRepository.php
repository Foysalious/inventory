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

    public function getDeletionFileNameCollectionImageFromCDN($partner_id, $collection_id, $column_name)
    {
        dd($this->model->where('id', $collection_id)->first()[$column_name]);
        return $this->model->where('id', $collection_id)->first()[$column_name] ?? [];
    }
}

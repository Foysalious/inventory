<?php
namespace App\Repositories;

use App\Interfaces\OptionRepositoryInterface;
use App\Models\Option;

class OptionRepository extends BaseRepository implements OptionRepositoryInterface
{
    public function __construct(Option $model)
    {
        parent::__construct($model);
    }

    public function getAllWithOptions($offset = 0, $limit = 50)
    {
        return $this->model->with(['values' => function ($q) use($offset, $limit) {
            $q->select('id', 'name', 'details', 'option_id');
        }])->skip($offset)->take($limit)->get();
    }
}

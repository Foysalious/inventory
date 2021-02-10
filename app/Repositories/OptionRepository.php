<?php namespace App\Repositories;

use App\Interfaces\OptionRepositoryInterface;
use App\Models\Option;

class OptionRepository extends BaseRepository implements OptionRepositoryInterface
{
    public function __construct(Option $model)
    {
        parent::__construct($model);
    }
}

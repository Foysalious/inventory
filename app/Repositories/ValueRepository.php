<?php namespace App\Repositories;


use App\Interfaces\ValueRepositoryInterface;
use App\Models\Value;

class ValueRepository extends BaseRepository implements ValueRepositoryInterface
{
    public function __construct(Value $model)
    {
        parent::__construct($model);
    }
}

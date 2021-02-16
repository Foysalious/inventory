<?php namespace App\Repositories;
use App\Interfaces\UnitRepositoryInterface;
use App\Models\Unit;

class UnitRepository extends BaseRepository implements UnitRepositoryInterface
{
    public function __construct(Unit $model)
    {
        parent::__construct($model);
    }


}

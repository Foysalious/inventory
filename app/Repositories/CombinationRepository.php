<?php namespace App\Repositories;

use App\Interfaces\CombinationRepositoryInterface;
use App\Models\Combination;

class CombinationRepository extends BaseRepository implements CombinationRepositoryInterface
{
    public function __construct(Combination $model)
    {
        parent::__construct($model);
    }

}

<?php

namespace App\Repositories;

use App\Models\Collection;

/**
 * Class CollectionRepository.
 */
class CollectionRepository extends BaseRepository
{
    /**
     * @param Collection $model
     */

    public function __construct(Collection $model)
    {
        parent::__construct($model);
    }
}

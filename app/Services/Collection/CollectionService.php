<?php


namespace App\Services\Collection;


use App\Repositories\CollectionRepository;
use App\Traits\ResponseAPI;

class CollectionService
{
    use ResponseAPI;

    protected $collectionRepository;

    public function __construct(CollectionRepository $collectionRepository)
    {
        $this->collectionRepository = $collectionRepository;
    }

    function getAllCollection() {
        try {



        } catch (\Exception $exception) {

            return $this->error($exception->getMessage());

        }
    }
}

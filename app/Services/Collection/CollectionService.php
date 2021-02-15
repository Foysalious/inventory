<?php


namespace App\Services\Collection;


use App\Repositories\CollectionRepository;

class CollectionService
{
    protected $collectionRepository;

    public function __construct(CollectionRepository $collectionRepository)
    {
        $this->collectionRepository = $collectionRepository;
    }
}

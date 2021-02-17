<?php


namespace App\Services\Collection;


use App\Http\Resources\CollectionResource;
use App\Repositories\CollectionRepository;
use App\Traits\ResponseAPI;
use App\Interfaces\CollectionRepositoryInterface;

class CollectionService
{
    use ResponseAPI;

    protected $collectionRepository;

    protected $collectionRepositoryInterface;

    public function __construct(CollectionRepository $collectionRepository, CollectionRepositoryInterface $collectionRepositoryInterface)
    {
        $this->collectionRepository = $collectionRepository;

        $this->collectionRepositoryInterface = $collectionRepositoryInterface;
    }

    function getAll() : object{
        try {

            $resource = $this->collectionRepositoryInterface->getAllCollection();

            $options = CollectionResource::collection($resource);

            return $this->success("Successful", $options);

        } catch (\Exception $exception) {

            return $this->error($exception->getMessage());

        }
    }
}

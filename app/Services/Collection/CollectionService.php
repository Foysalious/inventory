<?php


namespace App\Services\Collection;


use App\Http\Resources\CollectionResource;
use App\Repositories\CollectionRepository;
use App\Traits\ResponseAPI;
use App\Interfaces\CollectionRepositoryInterface;
use Illuminate\Http\Request;

class CollectionService
{
    use ResponseAPI;

    protected $collectionRepository;

    protected $collectionRepositoryInterface;

    protected $creator;

    public function __construct(CollectionRepository $collectionRepository, CollectionRepositoryInterface $collectionRepositoryInterface, Creator $creator)
    {
        $this->collectionRepository = $collectionRepository;

        $this->collectionRepositoryInterface = $collectionRepositoryInterface;

        $this->creator = $creator;
    }

    public function getAll() : object{
        try {

            $resource = $this->collectionRepositoryInterface->getAllCollection();

            $options = CollectionResource::collection($resource);

            return $this->success("Successful", $options);

        } catch (\Exception $exception) {

            return $this->error($exception->getMessage());

        }
    }

    public function create(Request $request)
    {

    }
}

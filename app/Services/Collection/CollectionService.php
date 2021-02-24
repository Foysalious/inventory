<?php


namespace App\Services\Collection;


use App\Http\Requests\CollectionRequest;
use App\Http\Resources\CollectionResource;
use App\Repositories\CollectionRepository;
use App\Traits\ResponseAPI;
use App\Interfaces\CollectionRepositoryInterface;
use Illuminate\Container\EntryNotFoundException;
use Illuminate\Http\Request;
use Mockery\Exception;
use Whoops\Exception\ErrorException;

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

    public function getDetails($collection)
    {
        try {
            $resource = $this->collectionRepositoryInterface->find($collection);
            $collection = new CollectionResource($resource);
            return $this->success('Successful', $collection, 200);
        } catch(\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function getAll(Request $request) : object{
        $resource = $this->collectionRepositoryInterface->getAllCollection($request);
        $options = CollectionResource::collection($resource);
        return $this->success("Successful", $options);
    }

    public function create(CollectionRequest $request)
    {
        $option = $this->creator->setName($request->name)
            ->setModifyBy($request->modifier)
            ->setDescription($request->description)
            ->setIsPublished($request->is_published)
            ->setPartnerId($request->partner_id)
            ->setThumb($request->thumb)
            ->setBanner($request->banner)
            ->setAppThumb($request->app_thumb)
            ->setAppBanner($request->app_banner)
            ->create();

        return $this->success("Successful", $option,201);
    }

    public function delete($collection)
    {
        try {
            $collection = $this->collectionRepositoryInterface->find($collection);
            $collection_id = $collection->id;
            $this->collectionRepositoryInterface->where('id', $collection_id)->delete();
            return $this->success("Successful",200);
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }

    }
}

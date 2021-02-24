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

    protected $creator, $updater;

    public function __construct(CollectionRepository $collectionRepository, CollectionRepositoryInterface $collectionRepositoryInterface, Creator $creator, Updater $updater)
    {
        $this->collectionRepository = $collectionRepository;
        $this->collectionRepositoryInterface = $collectionRepositoryInterface;
        $this->creator = $creator;
        $this->updater = $updater;
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
        try {
            list($offset, $limit) = calculatePagination($request);
            $resource = $this->collectionRepositoryInterface->getAllCollection($offset, $limit);
            $options = CollectionResource::collection($resource);
            return $this->success("Successful", $options, 201);
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }
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

    public function update(CollectionRequest $request, $collection_id)
    {
        $collection = $this->collectionRepositoryInterface->find($collection_id);
        if(!$collection) return $this->error("Collection not found!", 404);

        $option = $this->updater->setCollection($collection)->setName($request->name)
            ->setModifyBy($request->modifier)
            ->setDescription($request->description)
            ->setPartnerId($request->partner_id)
            ->setThumb($request->thumb)
            ->setBanner($request->banner)
            ->setAppThumb($request->app_thumb)
            ->setAppBanner($request->app_banner)
            ->update();

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

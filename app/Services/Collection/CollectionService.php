<?php namespace App\Services\Collection;


use App\Http\Requests\CollectionRequest;
use App\Http\Resources\CollectionResource;
use App\Repositories\CollectionRepository;
use App\Services\BaseService;
use App\Interfaces\CollectionRepositoryInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;

class CollectionService extends BaseService
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

    public function getAll(Request $request) : object{
        try {
            list($offset, $limit) = calculatePagination($request);
            $resource = $this->collectionRepositoryInterface->getAllCollection($offset, $limit);
            $options = CollectionResource::collection($resource);
            if(!$options) return $this->error("Collection not found!", 404);

            return $this->success("Successful", $options, 200);
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function getDetails($partnerId, $collection)
    {
        try {
            $resource = $this->collectionRepositoryInterface->find($collection);
            $collection = new CollectionResource($resource);
            return $this->success('Successful', $collection, 200);
        } catch(\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function create($partner_id, CollectionRequest $request)
    {
        $option = $this->creator->setName($request->name)
            ->setModifyBy($request->modifier)
            ->setDescription($request->description)
            ->setIsPublished($request->is_published)
            ->setPartnerId($partner_id)
            ->setThumb($request->thumb)
            ->setBanner($request->banner)
            ->setAppThumb($request->app_thumb)
            ->setAppBanner($request->app_banner)
            ->setShardingId($request->sharding_id)
            ->create();

        return $this->success("Successful", $option,201);
    }

    public function update(CollectionRequest $request, $partner_id, $collection_id)
    {
        $collection = $this->collectionRepositoryInterface->find($collection_id);
        if(!$collection) return $this->error("Collection not found!", 404);

        $option = $this->updater->setCollection($collection)->setName($request->name)
            ->setModifyBy($request->modifier)
            ->setDescription($request->description)
            ->setPartnerId($partner_id)
            ->setThumb($request->thumb)
            ->setBanner($request->banner)
            ->setAppThumb($request->app_thumb)
            ->setAppBanner($request->app_banner)
            ->setShardingId($request->sharding_id)
            ->update();

        return $this->success("Successful", $option,201);
    }

    public function delete($partner_id, $collection)
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

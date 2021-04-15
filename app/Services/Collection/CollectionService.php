<?php namespace App\Services\Collection;


use App\Constants\ImageConstants;
use App\Http\Requests\CollectionRequest;
use App\Http\Resources\CollectionResource;
use App\Repositories\CollectionRepository;
use App\Services\BaseService;
use App\Interfaces\CollectionRepositoryInterface;
use App\Services\FileManagers\CdnFileManager;
use Illuminate\Http\Request;

class CollectionService extends BaseService
{
    use CdnFileManager;

    protected $collectionRepository, $imageUpdater;
    protected $collectionRepositoryInterface;
    protected $creator, $updater;

    public function __construct(CollectionRepository $collectionRepository, CollectionRepositoryInterface $collectionRepositoryInterface, Creator $creator, Updater $updater, ImageUpdater $imageUpdater)
    {
        $this->collectionRepository = $collectionRepository;
        $this->collectionRepositoryInterface = $collectionRepositoryInterface;
        $this->creator = $creator;
        $this->updater = $updater;
        $this->imageUpdater = $imageUpdater;
    }

    public function getAllAccordingToPartnerID(Request $request, $partner_id) : object{
        try {
            list($offset, $limit) = calculatePagination($request);
            $resource = $this->collectionRepositoryInterface->getAllCollection($offset, $limit, $partner_id);
            $collections = CollectionResource::collection($resource);
            if(!$collections) return $this->error("Collection not found!", 404);

            return $this->success("Successful", ['collections' => $collections], 200);
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function getDetails($partnerId, $collectionId)
    {
        try {
            $singleCollection = $this->collectionRepositoryInterface->where('partner_id', $partnerId)->find($collectionId);
            if(!$singleCollection) return $this->error("কালেকশন পাওয়া যায় নি!", 404);

            $collection = new CollectionResource($singleCollection);
            return $this->success('Successful', ['collection' => $collection], 200);
        } catch(\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function create($partner_id, $request)
    {
        dd('Products: ' . $request['products']);
        $this->creator->setName($request['name'])
            ->setDescription($request['description'] ?? '')
            ->setIsPublished($request['is_published'])
            ->setPartnerId($partner_id)
            ->setThumb($request['thumb'] ?? '')
            ->setBanner($request['banner'] ?? '')
            ->setAppThumb($request['app_thumb'] ?? '')
            ->setAppBanner($request['app_banner'] ?? '')
            ->setProducts($request['products'])
            ->create();

        return $this->success("Successful",null, 201, true);
    }

    public function update($request, $partner_id, $collection_id)
    {
        $collection = $this->collectionRepositoryInterface->where('partner_id', $partner_id)->find($collection_id);
        if(!$collection) return $this->error("কালেকশন পাওয়া যায় নি!", 404);

        $this->updater->setCollection($collection)->setName($request->name)
            ->setCollectionId($collection_id)
            ->setDescription($request->description)
            ->setPartnerId($partner_id)
            ->setThumb($request->thumb)
            ->setBanner($request->banner)
            ->setAppThumb($request->app_thumb)
            ->setAppBanner($request->app_banner)
            ->setIsPublished($request->is_published)
            ->setProducts($request->products)
            ->update($request);

        return $this->success("Successful", null,200, true);
    }

    public function delete($partner_id, $collection_id)
    {
        $collection = $this->collectionRepositoryInterface->find($collection_id);
        if(!$collection) {
            return $this->error("কালেকশন পাওয়া যায় নি", 404);
        }
        try {
            $this->imageUpdater->deleteAllCollectionImages($partner_id, $collection_id);
            $collection->delete();
            return $this->success("Successful", null , 200, true);
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }

    }
}

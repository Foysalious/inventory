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
            $collections = CollectionResource::collection($resource);
            if(!$collections) return $this->error("Collection not found!", 404);

            return $this->success("Successful", ['collections' => $collections], 200);
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function getDetails($partnerId, $collection)
    {
        try {
            $resource = $this->collectionRepositoryInterface->find($collection);
            $collection = new CollectionResource($resource);
            return $this->success('Successful', ['collections' => $collection], 200);
        } catch(\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function create($partner_id, $request)
    {
        $collection = $this->creator->setName($request['name'])
            ->setDescription($request['description'] ?? '')
            ->setIsPublished($request['is_published'])
            ->setPartnerId($partner_id)
            ->setThumb($request['thumb'] ?? '')
            ->setBanner($request['banner'] ?? '')
            ->setAppThumb($request['app_thumb'] ?? '')
            ->setAppBanner($request['app_banner'] ?? '')
            ->create();

        return $this->success("Successful", ['collection' => $collection],201);
    }

    public function update($request, $partner_id, $collection_id)
    {
        $collection = $this->collectionRepositoryInterface->find($collection_id);
        if(!$collection) return $this->error("Collection not found!", 404);

        $collection_update = $this->updater->setCollection($collection)->setName($request['name'])
            ->setCollectionId($collection_id)
            ->setDescription($request['description'] ?? '')
            ->setPartnerId($partner_id)
            ->setThumb($request['thumb'] ?? '')
            ->setBanner($request['banner'] ?? '')
            ->setAppThumb($request['app_thumb'] ?? '')
            ->setAppBanner($request['app_banner'] ?? '')
            ->setIsPublished($request['is_published'])
            ->update();

        return $this->success("Successful", ['collection' => $collection_update],201);
    }

    public function delete($partner_id, $collection)
    {
        try {
            $collection = $this->collectionRepositoryInterface->find($collection);
            $collection_id = $collection->id;

            foreach (ImageConstants::COLLECTION_IMAGE_COLUMNS as $column_name)
            {
                $fileName = $this->collectionRepository->getDeletionFileNameCollectionImageFromCDN($partner_id, $collection_id, $column_name);
                if(isset($fileName))
                {
                    $storagePath = config('s3.url');
                    $this->deleteFileFromCDN(substr($fileName, strlen($storagePath)));
                }
            }
            $this->collectionRepositoryInterface->where('id', $collection_id)->delete();
            return $this->success("Successful",null, 200);
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }

    }
}

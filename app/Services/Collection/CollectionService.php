<?php


namespace App\Services\Collection;


use App\Http\Requests\CollectionRequest;
use App\Http\Resources\CollectionResource;
use App\Repositories\CollectionRepository;
use App\Traits\ResponseAPI;
use App\Interfaces\CollectionRepositoryInterface;

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
}

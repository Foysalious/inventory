<?php namespace App\Services\Webstore\Collection;
use App\Http\Resources\Webstore\WebstoreCollectionResource;
use App\Interfaces\CategoryRepositoryInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use App\Interfaces\CollectionRepositoryInterface;

class CollectionService
{
    use ResponseAPI;

    private CollectionRepositoryInterface $collectionRepositoryInterface;

    public function __construct(CollectionRepositoryInterface $collectionRepositoryInterface)
    {
        $this->collectionRepositoryInterface = $collectionRepositoryInterface;
    }

    public function getCollectionsByPartner($request, int $partner_id){
        list($offset, $limit) = calculatePagination($request);
        $resource = $this->collectionRepositoryInterface->getAllCollectionforWebstore($offset, $limit,$partner_id);
        if(!$resource) return $this->error("Collection not found!", 404);
        $collections = WebstoreCollectionResource::collection($resource);

        return $this->success("Successful", ['collections' => $collections], 200);
    }
}

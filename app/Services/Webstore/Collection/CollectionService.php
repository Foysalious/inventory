<?php namespace App\Services\Webstore\Collection;
use App\Http\Resources\CollectionResource;
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

    public function getAllAccordingToPartnerID($request, $partner_id){
        list($offset, $limit) = calculatePagination($request);
        $resource = $this->collectionRepositoryInterface->getAllCollectionforWebstore($offset, $limit,$partner_id);

        $collections = CollectionResource::collection($resource);
        if(!$collections) return $this->error("Collection not found!", 404);
        return $this->success("Successful", ['collections' => $collections], 200);
    }
}

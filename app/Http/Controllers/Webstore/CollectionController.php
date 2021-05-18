<?php namespace App\Http\Controllers\Webstore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Webstore\Collection\CollectionService;

class CollectionController extends Controller
{
    /**
     * @var CollectionService
     */
    private CollectionService $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    public function getAllCollection(Request $request,$partner_id)
    {
        return $this->collectionService->getAllAccordingToPartnerID($request, $partner_id);
    }
}

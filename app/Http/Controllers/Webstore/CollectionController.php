<?php namespace App\Http\Controllers\Webstore;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
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
    /**
     *
     * * @OA\Get(
     *      path="/api/v1/partners/{partner}/webstore/collections",
     *      operationId="getCollection",
     *      tags={"Partners Collection API"},
     *      summary="Get Collection By Partner ID",
     *      description="",
     *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="")),
     *     )
     * @param $partner_id
     * @return JsonResponse
     */
    public function index(Request $request,$partner_id)
    {
        return $this->collectionService->getCollectionsByPartner($request, $partner_id);
    }
}

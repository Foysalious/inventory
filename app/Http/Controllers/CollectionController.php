<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionRequest;
use App\Http\Requests\CollectionUpdateRequest;
use App\Services\Collection\CollectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionController extends Controller
{

    public function __construct(public CollectionService $collectionService){}

    /**
     *
     * * @OA\Get(
     *      path="/api/v1/partners/{partner}/collections",
     *      operationId="getAllAccordingToPartnerID",
     *      tags={"Partners Collection API"},
     *      summary="Get All Collection by Partner",
     *      description="Getting all collection with counting products under this collection by Partner and offset limit",
     *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="")),
     *      @OA\Response(response=404, description="message: কালেকশন পাওয়া যায় নি!")
     *     )
     * @param $partner_id
     * @return JsonResponse
     */
    public function index(Request $request, $partner_id) : object
    {
        return $this->collectionService->getAllAccordingToPartnerID($request, $partner_id);
    }

    /**
        * @OA\Post(
        *      path="/api/v1/partners/{partner}/collections",
        *      operationId="create",
        *      tags={"Partners Collection API"},
        *      summary="To create a collection",
        *      description="creating partners collection",
        *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
        *      @OA\RequestBody(
        *          @OA\MediaType(mediaType="multipart/form-data",
        *              @OA\Schema(
        *                  @OA\Property(property="name", type="String"),
        *                  @OA\Property(property="is_published", type="Integer"),
        *                  @OA\Property(property="products", type="Array"),
        *                  @OA\Property(property="thumb", type="file"),
        *                  @OA\Property(property="app_thumb", type="file"),
        *                  @OA\Property(property="banner", type="file"),
        *                  @OA\Property(property="app_banner", type="file")
        *             )
        *         )
        *      ),
        *      @OA\Response(
        *          response=201,
        *          description="Successful operation",
        *          @OA\JsonContent(ref="")
        *       ),
        *      @OA\Response(
        *          response=401,
        *          description="Unauthenticated",
        *      ),
        *      @OA\Response(
        *          response=403,
        *          description="Forbidden"
        *      )
        *     )
     */
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store($partner_id, CollectionRequest $request)
    {
        return $this->collectionService->create($partner_id, $request->validated());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     *
     * * @OA\Get(
     *      path="/api/v1/partners/{partner}/collections/{collection}",
     *      operationId="getDetails",
     *      tags={"Partners Collection API"},
     *      summary="Get a specific Collection details by Partner",
     *      description="Getting a collection with counting products under this collection by Partner and offset limit",
     *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="collection", description="collection id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="")),
     *      @OA\Response(response=404, description="message: কালেকশন পাওয়া যায় নি!")
     *     )
     * @param $partner_id
     * @return JsonResponse
     */

    public function show($partner_id, $collection_id)
    {
        return $this->collectionService->getDetails($partner_id, $collection_id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * @OA\Put(
     *      path="/api/v1/partners/{partner}/collections/{collection}",
     *      operationId="update",
     *      tags={"Partners Collection API"},
     *      summary="To update a collection",
     *      description="updating partners collection",
     *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="collection", description="collection id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          @OA\MediaType(mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="name", type="String"),
     *                  @OA\Property(property="is_published", type="Integer"),
     *                  @OA\Property(property="products", type="Array"),
     *                  @OA\Property(property="thumb", type="file"),
     *                  @OA\Property(property="app_thumb", type="file"),
     *                  @OA\Property(property="banner", type="file"),
     *                  @OA\Property(property="app_banner", type="file")
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     *
     **/

    public function update(CollectionUpdateRequest $request, $partner_id, $collection_id)
    {
        return $this->collectionService->update($request, $partner_id, $collection_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * @param $partner_id
     * @param $collection_id
     * @return JsonResponse
     */

    /**
     * @OA\Delete (
     *      path="/api/v1/partners/{partner}/collections/{collection}",
     *      operationId="deletingcollection",
     *      tags={"Partners Collection API"},
     *      summary="To delete a Collection",
     *      description="deleting Collection",
     *      @OA\Parameter (name="partner", description="partner id", required=true, in="path",@OA\Schema(type="integer")),
     *      @OA\Parameter (name="collection", description="partner id", required=true, in="path",@OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */

    public function destroy($partner_id, $collection_id)
    {
        return $this->collectionService->delete($partner_id, $collection_id);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionRequest;
use App\Http\Requests\CollectionUpdateRequest;
use App\Services\Collection\CollectionService;
use Illuminate\Http\Request;

class CollectionController extends Controller
{

    public function __construct(public CollectionService $collectionService){}

    public function index(Request $request, $partner_id) : object
    {
        return $this->collectionService->getAllAccordingToPartnerID($request, $partner_id);
    }

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
    public function destroy($partner_id, $collection_id)
    {
        return $this->collectionService->delete($partner_id, $collection_id);
    }
}

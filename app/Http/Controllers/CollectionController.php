<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionRequest;
use App\Models\Collection;
use App\Services\Collection\CollectionService;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    use ResponseAPI;

    protected $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }


    public function index(Request $request) : object
    {
        try {
            return $this->collectionService->getAll($request);
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(CollectionRequest $request)
    {
        return $this->collectionService->create($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\Response
     */
    public function show($collection)
    {
        return $this->collectionService->getDetails($collection);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Collection $collection)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Collection  $collection
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($collection)
    {
        return $this->collectionService->delete($collection);
    }
}

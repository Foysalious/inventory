<?php namespace App\Http\Controllers;

use App\Http\Requests\ValueRequest;
use App\Http\Requests\ValueUpdateRequest;
use App\Services\Value\ValueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ValueController extends Controller
{
    /** @var ValueService */
    protected ValueService $valueService;

    /**
     * ValueController constructor.
     * @param ValueService $valueService
     */
    public function __construct(ValueService $valueService)
    {
        $this->valueService = $valueService;
    }

    /**
     * @param ValueRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function store(ValueRequest $request, $partnerId, $optionId)
    {
        return $this->valueService->create($request, $optionId);
    }

    /**
     * @param ValueUpdateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ValueUpdateRequest $request, $partnerId, $id)
    {
        return $this->valueService->update($request, $id);
    }
}

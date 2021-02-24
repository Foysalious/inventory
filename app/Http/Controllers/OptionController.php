<?php namespace App\Http\Controllers;

use App\Exceptions\OptionNotFoundException;
use App\Http\Requests\OptionRequest;
use App\Services\Option\OptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    /** @var OptionService */
    protected OptionService $optionService;

    public function __construct(OptionService $optionService)
    {
        $this->optionService = $optionService;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws OptionNotFoundException
     */
    public function index(Request $request)
    {
        return $this->optionService->getAll($request);
    }

    /**
     * Store a newly created resource in storage.
     * @param OptionRequest $request
     * @return JsonResponse
     */
    public function store(OptionRequest $request)
    {
        return $this->optionService->create($request);
    }

    public function update(OptionRequest $request, $optionId)
    {
        return $this->optionService->update($request, $optionId);
    }
}

<?php namespace App\Http\Controllers;

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
     * @return JsonResponse
     */
    public function index()
    {
        return $this->optionService->getAll();
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

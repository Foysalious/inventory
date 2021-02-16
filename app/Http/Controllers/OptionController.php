<?php namespace App\Http\Controllers;

use App\Services\Option\OptionService;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    /** @var OptionService */
    protected OptionService $optionService;

    public function __construct(OptionService $optionService)
    {
        $this->optionService = $optionService;
    }

    public function index()
    {
        return $this->optionService->getAll();
    }

    public function store(Request $request)
    {
        return $this->optionService->create($request);
    }
}

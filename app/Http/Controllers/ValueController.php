<?php namespace App\Http\Controllers;

use App\Services\Value\ValueService;
use Illuminate\Http\Request;

class ValueController extends Controller
{
    /** @var ValueService */
    protected ValueService $valueService;

    public function __construct(ValueService $valueService)
    {
        $this->valueService = $valueService;
    }

    public function store(Request $request, $option)
    {
        return $this->valueService->create($request, $option);
    }
}

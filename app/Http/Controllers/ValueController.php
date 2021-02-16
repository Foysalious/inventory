<?php namespace App\Http\Controllers;

use App\Http\Requests\ValueRequest;
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

    public function store(ValueRequest $request, $id)
    {
        return $this->valueService->create($request, $id);
    }

    public function update(Request $request, $id)
    {
        return $this->valueService->update($request, $id);
    }
}

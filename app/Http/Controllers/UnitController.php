<?php namespace App\Http\Controllers;

use App\Services\Unit\UnitService;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /** @var UnitService */
    protected UnitService $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function index()
    {
        return $this->unitService->getAll();
    }
}

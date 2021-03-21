<?php

namespace App\Http\Controllers;

use App\Services\Warranty\Units;
use Illuminate\Http\Request;

class WarrantyUnitController extends Controller
{
    protected $warranty_unit_service;

    public function __construct(Units $units)
    {
        $this->warranty_unit_service = $units;
    }

    public function index(Request $request)
    {
//        return $this->warranty_unit_service->getAllWarrantyUnits($request);
    }
}

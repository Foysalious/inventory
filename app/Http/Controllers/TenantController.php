<?php namespace App\Http\Controllers;

use App\Models\CustomTenantModel;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Tenant;

class TenantController extends Controller
{
    public function store(Request $request, CustomTenantModel $tenant)
    {
        $tenant->name = $request->name;
        $tenant->domain = $request->domain;
        $tenant->database = $request->database;
        $tenant->save();
        return response()->json(["code" => 200, 'message' => 'Success'], 200);

    }
}

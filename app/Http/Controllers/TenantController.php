<?php namespace App\Http\Controllers;

use App\Models\CustomTenantModel;
use App\Models\User;
use Illuminate\Http\Request;

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

    public function get(Request $request)
    {
        $tenant = CustomTenantModel::find(3);
        $tenant->makeCurrent();
        $users = User::all();
        return response()->json(["code" => 200, 'message' => $users], 200);

    }
}

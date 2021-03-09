<?php namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryPartner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataMigrationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $partner_categories = !is_array($request->partner_pos_categories) ? json_decode($request->partner_pos_categories,1) : $request->partner_pos_categories;
        $categories = !is_array($request->pos_categories) ? json_decode($request->pos_categories,1) : $request->pos_categories;
        Category::insertOrIgnore($categories);
        CategoryPartner::insertOrIgnore($partner_categories);
        return $this->success('Successful', null);
    }
}

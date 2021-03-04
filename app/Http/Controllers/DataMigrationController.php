<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryPartner;
use Illuminate\Http\Request;

class DataMigrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        foreach (json_decode($request->partner_pos_categories,1) as $partner_pos_category)
        {
            $pos_categories_collection = collect(json_decode($request->pos_categories,1));
            $category = Category::find($partner_pos_category['category_id']);
            if(!$category) {
                $cat = $pos_categories_collection->where('id', $partner_pos_category['category_id'])->first();
                $this->parentInsertFirst($cat, $pos_categories_collection);
            }
            unset($partner_pos_category['updated_by']);
            unset($partner_pos_category['created_by']);
            CategoryPartner::insertOrIgnore($partner_pos_category);
        }
    }

    public function parentInsertFirst($category, $pos_categories_collection)
    {
        if (isset($category['parent_id'])) {
            $cat = $pos_categories_collection->where('category_id', $category['parent_id'])->first();
            return $this->parentInsertFirst($cat, $pos_categories_collection);
        }
        Category::insertOrIgnore($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

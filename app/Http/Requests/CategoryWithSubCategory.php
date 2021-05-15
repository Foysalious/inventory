<?php


namespace App\Http\Requests;

use \Illuminate\Foundation\Http\FormRequest;

class CategoryWithSubCategory extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'category_name' => 'required',
            'category_thumb' => 'nullable',
            'sub_category.*.name' => 'required',
            'sub_category.*.thumb' => 'nullable'
        ];
    }
}

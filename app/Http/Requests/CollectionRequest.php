<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class CollectionRequest extends FormRequest
{
    public function authorize() : bool
    {
        return true;
    }

    public function rules() : array
    {
        return [
            'name' => 'required|string',
            'thumb' => 'required|mimes:jpg,jpeg,png',
            'banner' => 'required|mimes:jpg,jpeg,png',
            'app_thumb' => 'required|mimes:jpg,jpeg,png',
            'app_banner' => 'required|mimes:jpg,jpeg,png',
        ];
    }
}

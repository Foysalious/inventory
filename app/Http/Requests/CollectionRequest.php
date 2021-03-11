<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class CollectionRequest extends FormRequest
{
    /**
     * @var mixed
     */

    public function authorize() : bool
    {
        return true;
    }

    public function rules() : array
    {
        return [
            'name'              => 'required|string',
            'description'       => 'nullable',
            'is_published'      => 'required',
            'thumb'             => 'nullable|mimes:jpg,bmp,png,jpeg',
            'banner'            => 'nullable|mimes:jpg,bmp,png,jpeg',
            'app_thumb'         => 'nullable|mimes:jpg,bmp,png,jpeg',
            'app_banner'        => 'nullable|mimes:jpg,bmp,png,jpeg'
        ];
    }
}

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
            'name'          => 'required|string',
            'is_published'  => 'required',
            'partner_id'    => 'required'
        ];
    }
}

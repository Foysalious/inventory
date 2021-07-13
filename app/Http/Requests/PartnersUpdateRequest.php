<?php namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class PartnersUpdateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sub_domain' => 'sometimes|string',
            'vat_percentage' => 'sometimes|numeric'
        ];
    }

}

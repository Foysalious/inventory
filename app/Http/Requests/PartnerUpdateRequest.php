<?php namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class PartnerUpdateRequest extends FormRequest
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

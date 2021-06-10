<?php namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class SkuStockUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required',
            'product_id' => 'required',
            'operation' => 'required',
            'quantity' => 'required|numeric',
        ];
    }

}

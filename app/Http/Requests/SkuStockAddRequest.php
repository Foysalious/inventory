<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SkuStockAddRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sku_id' => 'required',
            'accounting_info' => 'required',
            'stock' => 'required|numeric',
            'cost' => 'required',
        ];
    }
}

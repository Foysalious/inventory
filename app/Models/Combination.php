<?php namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;

class Combination extends BaseModel
{
    use HasFactory;
    protected $guarded = ['id'];

    public function productOptionValue()
    {
        return $this->belongsTo(ProductOptionValue::class,'product_option_value_id');
    }


}

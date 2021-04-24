<?php namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Combination extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

    public function productOptionValue()
    {
        return $this->belongsTo(ProductOptionValue::class,'product_option_value_id');
    }


}

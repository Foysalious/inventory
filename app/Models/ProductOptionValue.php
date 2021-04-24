<?php namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOptionValue extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

    public function productOption()
    {
        return $this->belongsTo(ProductOption::class,'product_option_id');
    }

}

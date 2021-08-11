<?php namespace App\Models;


use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOption extends BaseModel
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes;

    protected $guarded = ['id'];
    protected $cascadeDeletes = ['productOptionValues'];

    public function productOptionValues()
    {
        return $this->hasMany(ProductOptionValue::class,'product_option_id');
    }



}

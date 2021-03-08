<?php namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductOption extends BaseModel
{
    use HasFactory;
    protected $guarded = ['id'];

    public function productOptionValues()
    {
        return $this->hasMany(ProductOptionValue::class,'product_option_id');
    }



}

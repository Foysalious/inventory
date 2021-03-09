<?php namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductOptionValue extends BaseModel
{
    use HasFactory;
    protected $guarded = ['id'];

    public function productOption()
    {
        return $this->belongsTo(ProductOption::class,'product_option_id');
    }

}

<?php namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOption extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $guarded = ['id'];

    public function productOptionValues()
    {
        return $this->hasMany(ProductOptionValue::class,'product_option_id');
    }

/*    public function delete()
    {
        $this->productOptionValues()->delete();
        return parent::delete();
    }*/

    public static function boot() {
        parent::boot();
        static::deleting(function($productOption) {
            $productOption->productOptionValues->each->delete();
        });
    }



}

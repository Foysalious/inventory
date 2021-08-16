<?php namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;

class Partner extends BaseModel
{
    use HasFactory;
    protected $guarded = ['id'];

    public function categories()
    {
        return $this->hasMany(CategoryPartner::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    function skus()
    {
        return $this->hasManyThrough(Sku::class, Product::class);
    }

    public function batches()
    {
        return $this->hasManyDeep(SkuBatch::class, [Product::class, Sku::class]);
    }

    public function productChannels()
    {
        return $this->hasManyThrough(ProductChannel::class, Product::class);
    }


}

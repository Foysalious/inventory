<?php namespace App\Models;



use Illuminate\Database\Eloquent\Relations\Relation;


Relation::morphMap([
    'product'=>'App\Models\product',
    'product_channel'=>'App\Models\product_channel',
    'sku_channel'=>'App\Models\SkuChannel',
    'sku'=>'App\Models\SKU',
    'collection'=>'App\Models\collection',
    'category'=>'App\Models\category',
]);

class Discount extends BaseModel
{
    protected $guarded = ['id'];
    protected $casts = ['amount' => 'double'];

    public function type()
    {
        return $this->morphTo();
    }
}

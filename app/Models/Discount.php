<?php namespace App\Models;



use Carbon\Carbon;
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

    public function scopeValid($query)
    {
        return $query->where([['start_date', '<=', Carbon::now()], ['end_date', '>=', Carbon::now()]]);
    }
}

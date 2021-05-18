<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkuChannel extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $guarded = ['id'];
    protected $casts = ['cost' => 'double', 'price' => 'double', 'wholesale_price' => 'double'];

    public function sku()
    {
        return $this->belongsTo(Sku::class, 'sku_id');
    }

    public function discounts()
    {
        Relation::morphMap(['sku_channel'=>'App\Models\SkuChannel']);
        return $this->morphMany(Discount::class, 'type', 'type', 'type_id');
    }
}

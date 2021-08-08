<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sku extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $guarded = ['id'];
    protected $casts = ['stock' => 'double'];

    public function skuChannels()
    {
        return $this->hasMany(SkuChannel::class,'sku_id');
    }

    public function combinations()
    {
        return $this->hasMany(Combination::class,'sku_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
    public function originalPrice()
    {
        return $this->skuChannels()->min('price');
    }

    public function batch()
    {
        return $this->hasMany(SkuBatch::class,'sku_id');
    }

    public function stock()
    {
        $last_batch = $this->batch->sortByDesc('id')->first();
        if (is_null($last_batch) || is_null($last_batch->stock)) return null;
        $total_stock = 0;
        $batches = $this->batch;
        $total_stock +=  $batches->sum('stock');
        return $total_stock;
    }

    public function getPurchaseUnitPrice()
    {
        $last_batch = $this->batch->sortByDesc('id')->first();
        return $last_batch ? $last_batch->cost : 0;

    }

    public function getLastBatchStock()
    {
        $last_batch = $this->batch->sortByDesc('id')->first();
        return $last_batch ? $last_batch->stock : null;
    }


}

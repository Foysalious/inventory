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
        $total_stock = 0;
        $batches = $this->batch()->get();
        if(count($batches) > 0) {
            foreach ($batches as $batch) {
                $total_stock = $total_stock + $batch->stock;
            }
        }
        return $total_stock;
    }

    public function getPurchaseUnitPrice()
    {
        return $this->batch()->orderByDesc('id')->first()->cost ?? 0;

    }

    public function getLastBatchStock()
    {
        return $this->batch()->orderByDesc('id')->first()->stock ?? null;
    }


}

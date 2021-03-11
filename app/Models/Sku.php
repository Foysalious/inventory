<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sku extends BaseModel
{
    use HasFactory;
    protected $guarded = ['id'];

    public function skuChannels()
    {
        return $this->hasMany(SkuChannel::class,'sku_id');
    }

    public function combinations()
    {
        return $this->hasMany(Combination::class,'sku_id');
    }
}

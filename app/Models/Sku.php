<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sku extends BaseModel
{
    use HasFactory;
    protected $guarded = ['id'];

    public function skuChannels()
    {
        return $this->hasMany(SkuChannel::class);
    }
}

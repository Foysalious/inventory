<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkuBatch extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $guarded = ['id'];
    protected $casts = ['stock' => 'double'];

    public function sku()
    {
        return $this->belongsTo(Sku::class,'id');
    }
}

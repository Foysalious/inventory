<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = ['vat_percentage' => 'double'];

    public function skus()
    {
        return $this->hasMany(Sku::class);
    }
}

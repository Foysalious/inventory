<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Product extends BaseModel
{
    use HasFactory, SoftDeletes, Searchable;

    protected $guarded = ['id'];
    protected $casts = ['vat_percentage' => 'double'];

    public function skus()
    {
        return $this->hasMany(Sku::class);
    }
}

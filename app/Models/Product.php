<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public function toSearchableArray()
    {
        $array = $this->toArray();

        $data = [
            'id' => $array['id'],
            'name' => $array['name'],
            'description' => $array['description'],
            'partner_id' => $array['partner_id'],
            'warranty_unit' => $array['warranty_unit']
        ];

        return $data;
    }

    public function productOptions()
    {
        return $this->hasMany(ProductOption::class,'product_id');
    }

    public function productChannels()
    {
        return $this->hasMany(ProductChannel::class,'product_id');
    }

}

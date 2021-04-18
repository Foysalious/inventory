<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends BaseModel
{
    use HasFactory;
    protected $guarded = ['id'];

    public function products()
    {
        return $this->hasManyThrough(Product::class, CollectionProduct::class, 'collection_id', 'product_id');
    }
}

<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = ['vat_percentage' => 'double'];
}

<?php namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryPartner extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $table = 'category_partner';

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

}

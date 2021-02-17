<?php namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartnerCategory extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

}

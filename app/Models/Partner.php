<?php namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;

class Partner extends BaseModel
{
    use HasFactory;
    protected $guarded = ['id'];

    public function categories()
    {
        return $this->hasMany(PartnerCategory::class);

    }


}

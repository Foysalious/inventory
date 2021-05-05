<?php namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductChannel extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    public function channel()
    {
        return $this->belongsTo(Channel::class,'channel_id');
    }
}

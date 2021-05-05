<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{

    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'channels';

    public function products()
    {
        return $this->hasMany(ProductChannel::class,'channel_id');
    }
}

<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Option extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    public function values()
    {
        return $this->hasMany(Value::class);
    }
}

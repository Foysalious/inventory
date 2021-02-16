<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'units';
}

<?php namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Combinations extends BaseModel
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
}

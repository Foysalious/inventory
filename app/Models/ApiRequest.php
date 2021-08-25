<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApiRequest extends BaseModel
{
    protected $guarded = ['id'];
    use HasFactory;
    public function orders()
    {
        return $this->belongsTo(Product::class, 'api_request_id', 'id');
    }
}

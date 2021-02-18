<?php namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];


    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function scopeParents($query)
    {
        $query->where('parent_id', null);
    }

    public function scopeChild($query)
    {
        $query->where('parent_id', '<>', null);
    }

    public function scopePublished($query)
    {
        $query->where('publication_status', 1);
    }



}

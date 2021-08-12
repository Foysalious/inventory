<?php namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends BaseModel
{
    use HasFactory, SoftDeletes;

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

    public function scopeParent($query)
    {
        $query->where('parent_id', null);
    }

    public function scopePublished($query)
    {
        $query->where('publication_status', 1);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function partners()
    {
        return $this->belongsToMany(Partner::class);
    }

    public function categoryPartner(): HasMany
    {
        return $this->hasMany(CategoryPartner::class, 'category_id');
    }

    public function deletedCategoryPartner(): HasMany
    {
        return $this->hasMany(CategoryPartner::class, 'category_id')->withTrashed();
    }

    public function isPublishedForSheba()
    {
        return $this->is_published_for_sheba;
    }
}

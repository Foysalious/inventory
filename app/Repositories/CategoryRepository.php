<?php namespace App\Repositories;


use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function getCategoriesByPartner($partner_id)
    {
        return $this->model->where(function ($q) use ($partner_id) {
                $q->whereHas('categoryPartner', function ($q) use ($partner_id) {
                    $q->where('partner_id', $partner_id);
                });
        })->with('children', function ($q) {
            $q->leftJoin('category_partner', 'category_partner.category_id', '=', 'categories.id')
                ->select('categories.id', 'categories.name', 'categories.parent_id', 'categories.thumb as app_thumb', 'categories.is_published_for_sheba', 'category_partner.is_default');
        })->where('parent_id', NULL)->get();

    }

    public function getProductsByCategoryId($category_id){

        return $this->model->where('id',$category_id)->get();
    }



}

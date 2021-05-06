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
        return $this->model->leftJoin('category_partner', 'categories.id', '=', 'category_partner.category_id')
            ->where('category_partner.partner_id', $partner_id)
            ->whereNull('categories.parent_id')->select('categories.*', 'category_partner.category_id as category_id', 'category_partner.partner_id as partner_id')->get();
    }

    public function getCategory($partner_id)
    {
        return $this->model->where(function ($q) use ($partner_id) {
            $q->where('is_published_for_sheba', 1)->orWhere(function ($q) use ($partner_id) {
                $q->where('is_published_for_sheba', 0)->whereHas('categoryPartner', function ($q) use ($partner_id) {
                    $q->where('partner_id', $partner_id);
                });
            });
        })->with(['children' => function ($q) {
            $q->select('id', 'name', 'parent_id', 'app_thumb as icon');
        }])->where('parent_id', NULL)->get();


    }

    public function getProductsByCategoryId($category_id){

        return $this->model->where('id',$category_id)->get();
    }



}

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
            $q->whereIn('is_published_for_sheba', [0, 1])
                ->whereHas('categoryPartner', function ($q) use ($partner_id) {
                    $q->where('partner_id', $partner_id);
                });
        })->with(['children' => function ($q) {
            $q->select('id', 'name', 'parent_id', 'thumb as icon');
        }])->where('parent_id', NULL)->get();

    }

    public function getProductsByCategoryId($category_id){

        return $this->model->where('id',$category_id)->get();
    }



}

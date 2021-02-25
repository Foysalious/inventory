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
        return $this->model->leftJoin('partner_categories', 'categories.id', '=', 'partner_categories.category_id')
            ->where('partner_categories.partner_id',$partner_id)
            ->whereNull('categories.parent_id')->get();
    }

    public function getCategory()
    {
        $childCategory=array();
        $parents = $this->model->where('parent_id', NULL)->get();
        foreach ($parents as $parent) {
            $childs = $this->model->select('name')->where('parent_id', $parent->id)->get();

            $childCategory[$parent->name] = $childs;
        }
         return $childCategory;


    }


}

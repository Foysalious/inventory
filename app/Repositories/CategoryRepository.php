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
            ->where('category_partner.partner_id',$partner_id)
            ->whereNull('categories.parent_id')->get();
    }

    public function getCategory($partner_id)
    {
        return $this->model->with('children')->where('parent_id',NULL)->get();

    }


}

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
        return $this->model->leftJoin('category_partners', 'categories.id', '=', 'category_partners.category_id')
            ->where('category_partners.partner_id',$partner_id)
            ->whereNull('categories.parent_id')->get();
    }


}

<?php namespace App\Repositories;


use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }
    public function getMasterCategoriesByPartner($partner_id)
    {
        return $this->model->leftJoin('partner_categories', 'categories.id', '=', 'partner_categories.category_id')
            ->where('partner_categories.partner_id',$partner_id)
            ->whereNull('categories.parent_id')->get();
    }


}

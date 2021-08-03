<?php namespace App\Interfaces;


interface CategoryRepositoryInterface extends BaseRepositoryInterface
{

    public function getCategoriesByPartner($partner_id);

    public function getCategoriesForWebstore($partner_id);

    public function getProductsByCategoryId($category_id);

    public function getSubCategoryIds(array $category_ids);
}


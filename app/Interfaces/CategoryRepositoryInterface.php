<?php namespace App\Interfaces;


interface CategoryRepositoryInterface extends BaseRepositoryInterface
{

    public function getCategoriesByPartner($partner_id, $updated_after);

    public function getCategoriesForWebstore($partner_id);

    public function getProductsByCategoryId($category_id);

    public function getSubCategoryIds(array $category_ids);

    public function getDeletedCategories($partner_id,$updated_after);
}


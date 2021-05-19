<?php namespace App\Interfaces;


interface CategoryRepositoryInterface extends BaseRepositoryInterface
{

    public function getCategoriesByPartner($partner_id);

    public function getMasterCategoryWebstore($partner_id);

    public function getProductsByCategoryId($category_id);
}


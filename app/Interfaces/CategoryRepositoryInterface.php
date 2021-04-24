<?php namespace App\Interfaces;


interface CategoryRepositoryInterface extends BaseRepositoryInterface
{

    public function getCategory($partner_id);

    public function getCategoriesByPartner($partner_id);

    public function getProductsByCategoryId($category_id);
}


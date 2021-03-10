<?php  namespace App\Interfaces;


interface CategoryRepositoryInterface extends BaseRepositoryInterface
{

public function getCategory();
public function getCategoriesByPartner($partner_id);
}


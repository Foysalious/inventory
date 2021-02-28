<?php  namespace App\Interfaces;


interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
public function getMasterCategoriesByPartner($partner_id);

public function getCategory();
public function getCategoriesByPartner($partner_id);
}

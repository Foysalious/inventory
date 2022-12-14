<?php namespace App\Interfaces;


interface PartnerRepositoryInterface extends BaseRepositoryInterface
{
    public function getProductsInfoByPartner(int $partnerId);
    public function getPartnerPublishedProductsCount(int $partnerId);
}

<?php namespace App\Interfaces;


interface SkuRepositoryInterface extends BaseRepositoryInterface
{
    public function getSkusByPartnerId($partnerId, $offset = 0, $limit = 50);
    public function getSkusByIdsAndChannel(array $skus, $channelId);
    public function getSkuDetails($channelId, $skuId);

}

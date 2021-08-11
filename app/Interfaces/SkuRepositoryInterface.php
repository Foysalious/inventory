<?php namespace App\Interfaces;


interface SkuRepositoryInterface extends BaseRepositoryInterface
{
    public function getSkusByPartnerId($partnerId, $offset = 0, $limit = 50);
    public function getSkusByIdsAndChannel(array $skus, $channelId, $partner_id);
    public function getSkuDetails($channelId, $skuId);
    public function getSkusWithTrashed(array $sku_ids, int $partner_id);
}

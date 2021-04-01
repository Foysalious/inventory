<?php namespace App\Repositories;

use App\Interfaces\SkuRepositoryInterface;
use App\Models\Sku;

class SkuRepository extends BaseRepository implements SkuRepositoryInterface
{
    public function __construct(Sku $model)
    {
        parent::__construct($model);
    }

    public function getSkusByPartnerId($partnerId, $offset = 0, $limit = 50)
    {
        return $this->model->leftJoin('products', 'skus.product_id', '=', 'products.id')
            ->where('products.partner_id', $partnerId)
            ->skip($offset)->take($limit)
            ->get();
    }

    public function getSkusByIdsAndChannel($channelId,array $skus)
    {
        return $this->model->whereIn('id', $skus)->with(['skuChannels' => function ($q) use ($channelId) {
                $q->where('channel_id', $channelId);
            },'product'])->get();

    }



}

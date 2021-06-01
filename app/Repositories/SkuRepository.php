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

        return $this->model->select('skus.id', 'skus.stock', 'products.id as product_id', 'products.category_id', 'products.warranty', 'products.warranty_unit', 'products.vat_percentage')->leftJoin('products', 'skus.product_id', '=', 'products.id')
            ->where('products.partner_id', $partnerId)->with(['skuChannels' => function ($q) {
                $q->select('id', 'sku_id', 'channel_id', 'cost', 'price', 'wholesale_price');
            }, 'product'])
            ->skip($offset)->take($limit)
            ->get();
    }

    public function getSkusByIdsAndChannel(array $skus,$channelId)
    {
        return $this->model->whereIn('id', $skus)->with(['skuChannels' => function ($q) use ($channelId) {
                $q->where('channel_id',$channelId)->select('id as  sku_channel_id','sku_id','channel_id','cost','price','wholesale_price');
            },'product'=> function($q) {
            $q->select('id','warranty','warranty_unit','vat_percentage', 'name', 'unit_id')->with('unit');
        }])->get();
    }

    public function getSkuDetails($channelId, $skuId)
    {
        return $this->model->where('id',$skuId)->with(['combinations' => function($q){
            $q->select('id','sku_id','product_option_value_id')->with('productOptionValue', function ($q) {
                $q->with('productOption');
            });
        }])->select('id')->first();
    }

}

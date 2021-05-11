<?php namespace App\Repositories;


use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\SkuChannel;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    protected $skuChannelModel;

    public function __construct(Product $model, SkuChannel $skuChannel)
    {
        $this->skuChannelModel = $skuChannel;
        parent::__construct($model);
    }

    public function getProductsByPartnerId($partnerId, $offset = 0, $limit = 50, $searchKey = null)
    {
        $q = $this->model->where('partner_id', $partnerId);
        if($searchKey)
            $q->where(function ($query) use ($searchKey) {
                $query->where('name', 'LIKE', '%' . $searchKey . '%')
                    ->orWhere('description', 'LIKE', '%' . $searchKey . '%');
            });
        return $q->skip($offset)->take($limit)->get();
    }

    public function getProductsByCategoryId($category_id)
    {
        return $this->model->where('category_id', $category_id)->get();
    }

    public function productChannelPrice($productId)
    {
        $sku_channel_price_array = array();
        $skus_of_product = $this->model->findOrFail($productId)->skus;
        foreach ($skus_of_product as $sku) {
            $sku_channel_price = $this->skuChannelModel::where('sku_id', $sku->id)->get();
            array_push($sku_channel_price_array, $sku_channel_price);
        }
        return $sku_channel_price_array;
    }

}

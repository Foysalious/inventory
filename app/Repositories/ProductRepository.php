<?php namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\SkuChannel;
use App\Services\Channel\Channels;
use Illuminate\Support\Facades\DB;

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

    public function searchProductFromWebstore($searchKey, $partnerId, $limit = 10, $offset = 0)
    {
        return $this->searchWebstoreProductsFromDB($searchKey, $partnerId)
            ->select('id', 'partner_id', 'category_id', 'name', 'description')
            ->skip($offset)->take($limit)->get();
    }

    private function searchWebstoreProductsFromDB($searchKey, $partnerId)
    {
        return $this->model->where(function ($q) use ($searchKey) {
            $q->where('name', 'LIKE', '%' . $searchKey . '%')
                ->orWhere('description', 'LIKE', '%' . $searchKey . '%');
        })->where('partner_id', $partnerId)->whereHas('skus', function ($q) {
            $q->select(DB::raw('SUM(stock) as total_stock'))
                ->havingRaw('total_stock > 0');
        })->whereHas('skuChannels', function ($q) {
            $q->where('channel_id', Channels::WEBSTORE);
        });
    }

}

<?php namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\SkuBatchRepositoryInterface;
use App\Models\Product;
use App\Models\SkuChannel;
use App\Services\Channel\Channels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use function Symfony\Component\Translation\t;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    protected $skuChannelModel;

    public function __construct(Product $model, SkuChannel $skuChannel)
    {
        $this->skuChannelModel = $skuChannel;
        parent::__construct($model);
    }

    public function getProductsByCategoryId($category_id)
    {
        return $this->model->where('category_id', $category_id)->get();
    }

    public function productInformation($productId)
    {
        return $this->model->where('id', $productId)->get();
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

    public function searchProductFromWebstore(string $searchKey, int $partnerId, $limit = 10, $offset = 0)
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
            $q->whereHas('batch', function ($q) {
                $q->select(DB::raw('SUM(stock) as total_stock'))
                    ->havingRaw('total_stock > 0');
            });
        })->whereHas('skuChannels', function ($q) {
            $q->where('channel_id', Channels::WEBSTORE);
        });
    }

    public function getProductsByPartnerQuery(int $partnerId)
    {
        return $this->model->select('id', 'category_id', 'name', 'vat_percentage', 'unit_id', 'app_thumb', 'created_at')
            ->where('partner_id', $partnerId)->with(['unit' => function($q) {
                $q->select('id', 'name_bn', 'name_en');
            }, 'category' => function($q) {
                $q->select('id', 'parent_id')->with(['parent' => function($q) {
                    $q->select('id');
                }]);
            }, 'skus' => function($q) {
                $q->select('id', 'product_id')->with(['batch' => function($q) {
                    $q->select('id', 'sku_id', 'stock', 'cost');
                }, 'combinations' => function($q) {
                    $q->select('id', 'sku_id', 'product_option_value_id')->with(['productOptionValue' => function($q) {
                        $q->select('id', 'product_option_id', 'name', 'details')->with(['productOption' => function($q) {
                            $q->select('id', 'product_id', 'name');
                        }]);
                    }]);
                }, 'skuChannels' => function($q) {
                    $q->select('id', 'sku_id', 'channel_id', 'price', 'wholesale_price')->with(['validDiscounts' => function($q) {
                        $q->select('id', 'amount', 'is_amount_percentage', 'created_at');
                    }]);
                }]);
            }
        ]);
    }

    public function getStockDataForAccounting(Product $product)
    {
        $data = [];
        $data['total_quantity'] = 0;
        $data['total_cost'] = 0;
        /** @var SkuBatchRepository $batch_repo */
        $batch_repo = App::make(SkuBatchRepositoryInterface::class);
        $batches = $batch_repo->whereIn('sku_id', $product->skus()->pluck('id'))->orderBy('id', 'desc')->get();
        $supplier_taken = false;
        foreach ($batches as $batch) {
            if (!$supplier_taken) {
                $data['supplier_id'] = $batch->supplier_id;
                $data['from_account'] = $batch->from_account;
                $supplier_taken = true;
            }
            $temp['id'] = $product->id;
            $temp['name'] = $product->name;
            $temp['quantity'] = $batch->stock;
            $temp['unit_price'] = $batch->cost;
            $data['total_quantity'] += $batch->stock;
            $data['total_cost'] += $batch->stock * $batch->cost ;
            $data ['returned_stock'] [] = $temp;
        }
        return $data;
    }
}

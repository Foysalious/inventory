<?php namespace App\Services\Product;


use App\Exceptions\ProductNotFoundException;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductChannelPriceResource;
use App\Http\Resources\ProductResource;
use App\Interfaces\ProductOptionRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductService extends BaseService
{
    /** @var ProductRepositoryInterface */
    protected ProductRepositoryInterface $productRepositoryInterface;
    /** @var Creator */
    protected Creator $creator;
    /** @var Updater */
    protected Updater $updater;

    protected $optionRepositoryInterface;
    protected $valueRepositoryInterface;
    protected $productOptionRepositoryInterface;
    protected $skuRepositoryInterface;

    public function __construct(ProductRepositoryInterface $productRepositoryInterface,ProductOptionRepositoryInterface $productOptionRepositoryInterface, Creator $creator, Updater $updater,SkuRepositoryInterface $skuRepositoryInterface)
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->productOptionRepositoryInterface = $productOptionRepositoryInterface;
        $this->creator = $creator;
        $this->updater = $updater;
        $this->skuRepositoryInterface = $skuRepositoryInterface;
    }

    /**
     * @param $partner
     * @param Request $request
     * @return JsonResponse
     * @throws ProductNotFoundException
     */
    public function getProductList($partner, Request $request)
    {
        list($offset, $limit) = calculatePagination($request);
        $resource = $this->productRepositoryInterface->getProductsByPartnerId($partner, $offset, $limit);
        if ($resource->isEmpty()) throw new ProductNotFoundException('স্টকে কোন পণ্য নেই! প্রয়োজনীয় তথ্য দিয়ে স্টকে পণ্য যোগ করুন।');
        $products = ProductResource::collection($resource);
        if ($request->has('filter_by'))
            $products = $this->filterProducts($products, $request->filter_by, $request->filter_values);
        if ($request->has('order_by')) {
            $order = ($request->order == 'desc') ? 'sortByDesc' : 'sortBy';
            $products = $products->$order($request->order_by, SORT_NATURAL | SORT_FLAG_CASE);
        }
        return $this->success('Successful', ['products' => $products], 200);
    }

    /**
     * @param $products
     * @param $by
     * @param $values
     * @return string
     */
    private function filterProducts($products, $by, $values)
    {
        switch ($by) {
            case 'category': return $products->whereIn('category_id',json_decode($values));
            case 'collection': return $products->whereIn('collection_id',json_decode($values));
            case 'price': return $products->whereBetween('original_price', json_decode($values));
            case 'rating': return $products->whereIn('rating', json_decode($values));
            default: return '';
        }
    }

    /**
     * @param $product
     * @return JsonResponse
     */
    public function getDetails($product)
    {
        $general_details = $this->productRepositoryInterface->findOrFail($product);
        list($options,$combinations) = $this->getCombinationData($general_details);
        $general_details->options = collect($options);
        $general_details->combinations = collect($combinations);
        $product = new ProductResource($general_details);
        return $this->success('Successful', ['product' => $product], 200);
    }

    /**
     * @param $product
     * @return array
     */
    private function getCombinationData($product)
    {
        $data = [];
        $options = $this->productOptionRepositoryInterface->where('product_id',$product->id)->pluck('name');
        $skus = $this->skuRepositoryInterface->where('product_id', $product->id)->with('combinations')->get();

        foreach ($skus as $sku) {
            $sku_data = [];
            $temp = [];
            if($sku->combinations)
            {
                $sku->combinations->each(function ($combination) use (&$sku_data, &$temp, &$data) {
                    $product_option_value = $combination->productOptionValue;
                    array_push($temp, [
                        'option_id' => $product_option_value->productOption->id,
                        'option_name' => $product_option_value->productOption->name,
                        'option_value_id' => $product_option_value->id,
                        'option_value_name' => $product_option_value->name
                    ]);
                });
            }

            if (!isset($sku_data['combination'])) $sku_data['combination'] = [];
            $sku_data['combination'] = !empty($temp)? $temp :null;
            if (!isset($sku_data['stock'])) $sku_data['stock'] = [];
            $sku_data['stock'] = $sku->stock;
            $temp = [];
            if($sku->skuChannels)
            {
                $sku->skuChannels->each(function ($sku_channel) use (&$temp) {
                    array_push($temp, [
                        "sku_channel_id" => $sku_channel->id,
                        "channel_id" => $sku_channel->channel_id,
                        "cost" => $sku_channel->cost,
                        "price" => $sku_channel->price,
                        "wholesale_price" => $sku_channel->wholesale_price
                    ]);
                });
            }

            if (!isset($sku_data['channel_data'])) $sku_data['channel_data'] = [];
            $sku_data['channel_data'] = !empty($temp)? $temp :null;
            array_push($data, $sku_data);
        }
        return [$options,$data];
    }

    /**
     * @param $partnerId
     * @param ProductCreateRequest $request
     * @return JsonResponse
     */
    public function create($partnerId, ProductRequest $request)
    {
        /** @var ProductDetailsObject[] $product_create_requests */
       list($has_variant,$product_create_request_objs) =  app(ProductCreateRequest::class)->setProductDetails($request->product_details)->get();
       $product = $this->creator->setPartnerId($partnerId)
            ->setCategoryId($request->category_id)
            ->setName($request->name)
            ->setDescription($request->description)
            ->setWarranty($request->warranty)
            ->setWarrantyUnit($request->warranty_unit)
            ->setVatPercentage($request->vat_percentage)
            ->setUnitId($request->unit_id)
            ->setDiscount($request->discount_amount)
            ->setDiscountEndDate($request->discount_end_date)
            ->setImages($request->images)
            ->setProductRequestObjects($product_create_request_objs)
            ->setHasVariant($has_variant)
            ->create();

        return $this->success("Successful", ['product' => $product],201);
    }

    /**
     * @param $productId
     * @param ProductUpdateRequest $request
     * @return JsonResponse
     */
    public function update($productId, ProductUpdateRequest $request)
    {
        $product = $this->productRepositoryInterface->findOrFail($productId);
        list($has_variant,$product_update_request_objs) =  app(ProductUpdateRequestObjects::class)->setProductDetails($request->product_details)->get();
        $this->updater->setProduct($product)
            ->setCategoryId($request->category_id)
            ->setName($request->name)
            ->setDescription($request->description)
            ->setWarranty($request->warranty)
            ->setWarrantyUnit($request->warranty_unit)
            ->setVatPercentage($request->vat_percentage)
            ->setUnitId($request->unit_id)
            ->setDiscount($request->discount_amount)
            ->setDiscountEndDate($request->discount_end_date)
            ->setImages($request->images)
            ->setProductUpdateRequestObjects($product_update_request_objs)
            ->setHasVariant($has_variant)
            ->update();
        return $this->success("Successful", ['product' => $product],200);
    }

    public function delete($productId)
    {
        $product = $this->productRepositoryInterface->findOrFail($productId)->delete();
        return $this->success("Successful", ['product' => $product],200, false);
    }
}

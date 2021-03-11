<?php namespace App\Services\Product;


use App\Exceptions\ProductNotFoundException;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductChannelPriceResource;
use App\Http\Resources\ProductResource;
use App\Interfaces\CombinationRepositoryInterface;
use App\Interfaces\OptionRepositoryInterface;
use App\Interfaces\ProductChannelRepositoryInterface;
use App\Interfaces\ProductOptionRepositoryInterface;
use App\Interfaces\ProductOptionValueRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Interfaces\ValueRepositoryInterface;
use App\Services\BaseService;
use App\Services\Discount\Creator as DiscountCreator;
use App\Services\ProductImage\Creator as ProductImageCreator;
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
    protected $productOptionValueRepositoryInterface;
    protected $combinationRepositoryInterface;
    protected $productChannelRepositoryInterface;
    protected $skuRepositoryInterface;

    public function __construct(ProductRepositoryInterface $productRepositoryInterface, Creator $creator, Updater $updater, DiscountCreator $discountCreator, ProductImageCreator $productImageCreator,
                                OptionRepositoryInterface $optionRepositoryInterface, ValueRepositoryInterface  $valueRepositoryInterface, ProductOptionRepositoryInterface $productOptionRepositoryInterface,
                                ProductOptionValueRepositoryInterface $productOptionValueRepositoryInterface, CombinationRepositoryInterface  $combinationRepositoryInterface, ProductChannelRepositoryInterface $productChannelRepositoryInterface,SkuRepositoryInterface $skuRepositoryInterface)
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
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
        return $this->success('Successful', $products, 200);
    }

    /**
     * @param $product
     * @return JsonResponse
     */
    public function getDetails($product)
    {
        $general_details = $this->productRepositoryInterface->findOrFail($product);
        $combinations = $this->getCombinationData($general_details);
        $general_details->combinations = collect($combinations);
        $product = new ProductResource($general_details);
        return $this->success('Successful', $product, 200);
    }

    private function getCombinationData($product)
    {

        $skus = $this->skuRepositoryInterface->where('product_id', $product->id)->with('combinations')->get();
        $data = [];
        foreach ($skus as $sku) {
            $sku_data = [];
            $temp = [];
            $sku->combinations->each(function ($combination) use (&$sku_data, &$temp, &$data) {
                $product_option_value = $combination->productOptionValue;
                $value = $product_option_value->name;
                $option = $product_option_value->productOption->name;
                array_push($temp, [
                    'option' => $option,
                    'value' => $value,
                ]);
            });
            if (!isset($sku_data['combination'])) $sku_data['combination'] = [];
            $sku_data['combination'] = $temp;
            if (!isset($sku_data['stock'])) $sku_data['stock'] = [];
            $sku_data['stock'] = $sku->stock;
            $temp = [];
            $sku->skuChannels->each(function ($sku_channel) use (&$temp) {
                array_push($temp, [
                    "channel_id" => $sku_channel->channel_id,
                    "cost" => $sku_channel->cost,
                    "price" => $sku_channel->price,
                    "wholesale_price" => $sku_channel->wholesale_price
                ]);
            });
            if (!isset($sku_data['channel_data'])) $sku_data['channel_data'] = [];
            $sku_data['channel_data'] = $temp;
            array_push($data, $sku_data);
        }
        return $data;
    }

    /**
     * @param $partnerId
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function create($partnerId, ProductRequest $request)
    {
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
            ->setProductDetails($request->product_details)
            ->create();

        return $this->success("Successful", $product,201);
    }

    /**
     * @param $productId
     * @param ProductUpdateRequest $request
     * @return JsonResponse
     */
    public function update($productId, ProductUpdateRequest $request)
    {
        $product = $this->productRepositoryInterface->findOrFail($productId);
        $this->updater->setProduct($product)
            ->setCategoryId($request->category_id)
            ->setName($request->name)
            ->setDescription($request->description)
            ->setWarranty($request->warranty)
            ->setWarrantyUnit($request->warranty_unit)
            ->setVatPercentage($request->vat_percentage)
            ->setUnitId($request->unit_id)
            ->setProductDetails($request->product_details)
            ->update();
        return $this->success("Successful", $product,200);
    }

    public function delete($productId)
    {
        $product = $this->productRepositoryInterface->findOrFail($productId)->delete();
        return $this->success("Successful", $product,200, false);
    }
}

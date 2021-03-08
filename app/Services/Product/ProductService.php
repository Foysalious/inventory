<?php namespace App\Services\Product;


use App\Exceptions\ProductNotFoundException;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Interfaces\ProductRepositoryInterface;
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

    public function __construct(ProductRepositoryInterface $productRepositoryInterface, Creator $creator, Updater $updater)
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->creator = $creator;
        $this->updater = $updater;
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
        $resource = $this->productRepositoryInterface->findOrFail($product);
        $this->getCombinationData($resource);
        $product = new ProductResource($resource);
        return $this->success('Successful', $product, 200);
    }

    private function getCombinationData($product)
    {
        $skus = $this->skuRepositoryInterface->where('product_id',$product->id)->with('combinations')->get();
        foreach($skus as $sku)
        {
            $p_o_v_s = $sku->combinations->pluck('product_option_value_id');
            $p_o_v = $this->productOptionValueRepositoryInterface->whereIn('id',$p_o_v_s)->select('product_option_id','name')->get();
        }
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

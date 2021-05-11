<?php namespace App\Services\Product;

use App\Exceptions\ProductNotFoundException;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\WebstoreProductResource;
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
    /** @var ProductCombinationService */
    private ProductCombinationService $productCombinationService;
    /**@var ProductList*/
    private ProductList $productList;

    public function __construct(
        ProductRepositoryInterface $productRepositoryInterface,
        ProductOptionRepositoryInterface $productOptionRepositoryInterface,
        Creator $creator,
        Updater $updater,
        SkuRepositoryInterface $skuRepositoryInterface,
        ProductCombinationService $productCombinationService,
        ProductList $productList
    )
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->productOptionRepositoryInterface = $productOptionRepositoryInterface;
        $this->creator = $creator;
        $this->updater = $updater;
        $this->skuRepositoryInterface = $skuRepositoryInterface;
        $this->productCombinationService = $productCombinationService;
        $this->productList = $productList;
    }

    /**
     * @param $partner
     * @param Request $request
     * @return JsonResponse
     * @throws ProductNotFoundException
     */
    public function getWebstoreProducts($partner, Request $request)
    {
        list($offset, $limit) = calculatePagination($request);
        $resource = $this->productRepositoryInterface->getProductsByPartnerId($partner, $offset, $limit);
        if ($resource->isEmpty()) throw new ProductNotFoundException('স্টকে কোন পণ্য নেই! প্রয়োজনীয় তথ্য দিয়ে স্টকে পণ্য যোগ করুন।');
        $products = WebstoreProductResource::collection($resource);
        if ($request->has('filter_by'))
            $products = $this->filterProducts($products, $request->filter_by, $request->filter_values);
        if ($request->has('order_by')) {
            $order = ($request->order == 'desc') ? 'sortByDesc' : 'sortBy';
            $products = $products->$order($request->order_by, SORT_NATURAL | SORT_FLAG_CASE);
        }
        return $this->success('Successful', ['products' => $products], 200);
    }

    /**
     * @param $partner_id
     * @param Request $request
     * @return JsonResponse
     * @throws ProductNotFoundException
     */
    public function getProducts($partner_id, Request $request)
    {
        list($offset, $limit) = calculatePagination($request);
        $category_ids = !is_array($request->category_ids) ? json_decode($request->category_ids,1) : $request->category_ids;
        $sub_category_ids = !is_array($request->sub_category_ids) ? json_decode($request->sub_category_ids,1) : $request->sub_category_ids;
        $this->productList->setPartnerId($partner_id)
            ->setCategoryIds($category_ids)
            ->setSubCategoryIds($sub_category_ids)
            ->setUpdatedAfter($request->updated_after)
            ->setWebstorePublicationStatus($request->is_published_for_webstore)
            ->setOffset($offset)
            ->setLimit($limit);
        $products = $this->productList->get();
        return $this->success("Successful", ['products_info' => $products]);
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
        list($options,$combinations) = $this->productCombinationService->setProduct($general_details)->getCombinationData();
        $general_details->options = collect($options);
        $general_details->combinations = collect($combinations);
        $product = new WebstoreProductResource($general_details);
        return $this->success('Successful', ['product' => $product], 200);
    }

    /**
     * @param $partnerId
     * @param ProductCreateRequest $request
     * @return JsonResponse
     */
    public function create($partnerId, ProductRequest $request)
    {
        /** @var ProductDetailsObject[] $product_create_requests */
       list($has_variant,$product_create_request_objs) = app(ProductCreateRequest::class)->setProductDetails($request->product_details)->get();
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

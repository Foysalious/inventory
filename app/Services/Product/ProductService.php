<?php namespace App\Services\Product;

use App\Events\ProductStockAdded;
use App\Events\ProductStockUpdated;
use App\Exceptions\ProductDetailsPropertyValidationError;
use App\Exceptions\ProductNotFoundException;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\WebstoreProductResource;
use App\Interfaces\ProductOptionRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Models\Product;
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
     * @param $partner_id
     * @param Request $request
     * @return JsonResponse
     * @throws ProductNotFoundException
     */
    public function getProducts($partner_id, Request $request): JsonResponse
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
        return $this->success("Successful", ['data' => $products]);
    }

    /**
     * @param $partner
     * @param $product
     * @return JsonResponse
     */
    public function getDetails($partner, $product): JsonResponse
    {
        $general_details = $this->productRepositoryInterface->findOrFail($product);
        if($general_details->partner_id != $partner)
            return $this->error("This product does not belongs to this partner", 403);
        $combinations = $this->productCombinationService->setProduct($general_details)->getCombinationData();
        $general_details->combinations = collect($combinations);
        $product = new WebstoreProductResource($general_details);
        return $this->success('Successful', ['product' => $product], 200);
    }

    /**
     * @param $partnerId
     * @param ProductRequest $request
     * @return JsonResponse
     * @throws ProductDetailsPropertyValidationError
     */
    public function create($partnerId, ProductRequest $request)
    {
        /** @var ProductCreateRequest $productCreateRequest */
        $productCreateRequest = app(ProductCreateRequest::class);
        list($has_variant,$product_create_request_objs) = $productCreateRequest->setProductDetails($request->product_details)->get();
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

//        if($product && $request->has('accounting_info')) {
//            event(new ProductStockAdded($product,$request));
//        }

        return $this->success("Successful", ['product' => $product],201);
    }

    /**
     * @param $productId
     * @param ProductUpdateRequest $request
     * @return JsonResponse
     */
    public function update($productId, ProductUpdateRequest $request, $partner)
    {
        $product = $this->productRepositoryInterface->findOrFail($productId);
        if($product->partner_id != $partner)
            return $this->error("This product does not belong this partner", 403);
        /** @var $productUpdateRequestObjects ProductUpdateRequestObjects */
        $productUpdateRequestObjects = app(ProductUpdateRequestObjects::class);
        list($has_variant,$product_update_request_objs) =  $productUpdateRequestObjects->setProductDetails($request->product_details)->get();

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
            ->setDeletedImages($request->deleted_images)
            ->setProductUpdateRequestObjects($product_update_request_objs)
            ->setHasVariant($has_variant)
            ->update();

        if($product && $request->has('accounting_info')) {
            event(new ProductStockUpdated($product,$request));
        }

        return $this->success("Successful", ['product' => $product],200);
    }

    public function delete($partner,$productId)
    {
        $product = $this->productRepositoryInterface->findOrFail($productId);
        if($product->partner_id != $partner)
            return $this->error("This product does not belong to this partner", 403);
        $product->delete();
        return $this->success("Successful", ['product' => $product],200, false);
    }
}

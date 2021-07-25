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
use App\Repositories\CategoryRepository;
use App\Services\BaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        ProductList $productList,
        protected CategoryRepository $categoryRepository
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
    public function getDetails($partner, $product_id): JsonResponse
    {
        $product = $this->productRepositoryInterface->find($product_id);
        if(!$product)
            return $this->error("Product is not found", 404);
        if($product->partner_id != $partner)
            return $this->error("This product does not belongs to this partner", 403);
        $combinations = $this->productCombinationService->setProduct($product)->getCombinationData();
        $product->combinations = collect($combinations);
        $product_resource = new WebstoreProductResource($product);
        return $this->success('Successful', ['product' => $product_resource], 200);
    }

    /**
     * @param $partnerId
     * @param ProductRequest $request
     * @return JsonResponse
     * @throws ProductDetailsPropertyValidationError
     */
    public function create($partnerId, ProductRequest $request)
    {
        $default_sub_category = $this->getDefaultSubCategory($partnerId, $request->category_id);
        /** @var ProductCreateRequest $productCreateRequest */
        $productCreateRequest = app(ProductCreateRequest::class);
        list($has_variant,$product_create_request_objs) = $productCreateRequest->setProductDetails($request->product_details)->get();
        $product = $this->creator->setPartnerId($partnerId)
            ->setCategoryId($request->sub_category_id ?? $default_sub_category)
            ->setName($request->name)
            ->setDescription($request->description)
            ->setWarranty($request->warranty)
            ->setWarrantyUnit($request->warranty_unit)
            ->setVatPercentage($request->vat_percentage)
            ->setUnitId($request->unit_id)
            ->setDiscount($request->discount_amount)
            ->setDiscountEndDate($request->discount_end_date)
            ->setImages($request->images)
            ->setAppThumb($request->app_thumb)
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
     * @param $partner
     * @return JsonResponse
     * @throws UnknownProperties
     */
    public function update($productId, ProductUpdateRequest $request, $partner): JsonResponse
    {
        $default_sub_category = $this->getDefaultSubCategory($partner, $request->category_id);
        $product = $this->productRepositoryInterface->findOrFail($productId);
        if($product->partner_id != $partner)
            return $this->error("This product does not belong this partner", 403);
        /** @var ProductUpdateRequestObjects $productUpdateRequestObjects */
        $productUpdateRequestObjects = app(ProductUpdateRequestObjects::class);
        /** @var ProductUpdateDetailsObjects[] $product_update_request_objs */
        /** @var bool $has_variant */
        list($has_variant, $product_update_request_objs) =  $productUpdateRequestObjects->setProductDetails($request->product_details)->get();
        $this->updater->setProduct($product)
            ->setCategoryId($request->sub_category_id ?? $default_sub_category)
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

//        if($product && $request->has('accounting_info')) {
//            event(new ProductStockUpdated($product,$request));
//        }

        return $this->success("Successful", [],200);
    }

    public function delete($partner,$productId)
    {
        $product = $this->productRepositoryInterface->findOrFail($productId);
        if($product->partner_id != $partner)
            return $this->error("This product does not belong to this partner", 403);
        $product->delete();
        return $this->success("Successful", ['product' => $product],200, false);
    }

    private function getDefaultSubCategory($partner_id, $category_id)
    {
        $sub_category = $this->categoryRepository->getDefaultSubCategory($partner_id, $category_id);
        if(is_null($sub_category)) {
            throw new NotFoundHttpException("This category does not belong to this partner");
        } else {
            return $sub_category->id;
        }
    }
}

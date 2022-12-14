<?php namespace App\Services\Product;

use App\Events\Accounting\ProductStockAdded;
use App\Events\Accounting\ProductStockUpdated;
use App\Exceptions\ProductDetailsPropertyValidationError;
use App\Exceptions\ProductNotFoundException;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductDetailsResource;
use App\Interfaces\ProductOptionRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Services\BaseService;
use App\Services\Product\Constants\Log\FieldType;
use App\Services\Usage\Types;
use App\Services\Usage\UsageService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        protected CategoryRepository $categoryRepository,
        protected UsageService $usageService
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
        $updated_after = $request->has('updated_after') ? convertTimezone(Carbon::parse($request->updated_after)->shiftTimezone('Asia/Dhaka'), 'UTC') : null;
        list($offset, $limit) = calculatePagination($request);
        $category_ids = !is_array($request->category_ids) ? json_decode($request->category_ids,1) : $request->category_ids;
        $sub_category_ids = !is_array($request->sub_category_ids) ? json_decode($request->sub_category_ids,1) : $request->sub_category_ids;
        $this->productList->setPartnerId($partner_id)
            ->setCategoryIds($category_ids)
            ->setSubCategoryIds($sub_category_ids)
            ->setUpdatedAfter($updated_after)
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
        $product_resource = new ProductDetailsResource($product);
        return $this->success('Successful', ['product' => $product_resource], 200);
    }

    /**
     * @param $partnerId
     * @param ProductRequest $request
     * @return JsonResponse
     * @throws ProductDetailsPropertyValidationError
     */
    public function create($partnerId, ProductRequest $request): JsonResponse
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
            ->setImages($request->images)
            ->setAppThumb($request->app_thumb)
            ->setProductRequestObjects($product_create_request_objs)
            ->setHasVariant($has_variant)
            ->setApiRequest($request->api_request->id)
            ->create();

            if ($product && $request->has('accounting_info')) {
                event(new ProductStockAdded($product,$request));
            }
            return $this->success("Successful", ['product' => $product], 201);
    }

    /**
     * @param $productId
     * @param ProductUpdateRequest $request
     * @param $partner
     * @return JsonResponse
     * @throws Exception
     */
    public function update($productId, ProductUpdateRequest $request, $partner): JsonResponse
    {
        $default_sub_category = $this->getDefaultSubCategory($partner, $request->category_id);
        $product = $this->productRepositoryInterface->where('id', $productId)->where('partner_id', $partner)->with('skus')->first();
        if(is_null($product))
            return $this->error("This product does not belong this partner", 403);
        $old_stock_data = $this->productRepositoryInterface->getStockDataForAccounting($product);
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
            ->setAccountingInfo($request->accounting_info ?? null)
            ->update();

        if($product && $request->has('accounting_info')) {
            event(new ProductStockUpdated($product,$request,$old_stock_data));
        }

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
        $category = Category::find($category_id);
        if (!$category) throw new NotFoundHttpException("Category Not Found");
        if($category->is_published_for_sheba) {
            $sub_category = Category::where('name', 'Sub None Category')->where('parent_id', $category_id)->first();
        } else {
            $sub_category = $this->categoryRepository->getDefaultSubCategory($partner_id, $category_id);
        }
        if(is_null($sub_category)) {
            throw new NotFoundHttpException("This category does not belong to this partner");
        } else {
            return $sub_category->id;
        }
    }

    public function getLogs($partner, $product) : JsonResponse
    {
        $product = $this->productRepositoryInterface->findOrFail($product);
        if($product->partner_id != $partner)
            throw new NotFoundHttpException("This product does not belong to this partner");
        $combinations = $this->productCombinationService->setProduct($product)->getCombinationData();
        $product->combinations = collect($combinations);
        $product = new ProductDetailsResource($product);
        $logs = [];
        $identifier = [
            FieldType::STOCK => $product->unit ? constants('POS_SERVICE_UNITS')[$product->unit['name_en']]['bn']: '?????????',
            FieldType::VAT => '%',
            FieldType::PRICE => '???',
            FieldType::CATEGORY_ID => '???????????????????????????',
            FieldType::NAME => '?????????',
            FieldType::UNIT => '?????????',
            FieldType::WARRANTY_UNIT => '??????????????????????????? ?????????',
            FieldType::WARRANTY => '???????????????????????????',
            FieldType::APP_THUMB => '?????????',
            FieldType::SUB_CATEGORY_ID => '????????? ???????????????????????????'
        ];

        $service = $product->load('logs');
        $displayable_field_name = FieldType::getFieldsDisplayableNameInBangla();
        $service->logs->sortByDesc('created_at')->each(function ($log) use (&$logs, $displayable_field_name, $identifier) {
            collect(json_decode($log->field_names))->each(function ($field) use (&$logs, $log, $displayable_field_name, $identifier) {
                if (!in_array($field, FieldType::fields())) return false;
                array_push($logs, [
                    'log_type' => $field,
                    'log_type_show_name' => [
                        'bn' => $displayable_field_name[$field]['bn'],
                        'en' => $displayable_field_name[$field]['en']
                    ],
                    'log' => [
                        'bn' => $this->generateBanglaLog($field, $log, $identifier)
                    ],
                    'created_by' => $log->created_by_name ?? '',
                    'created_at' => isset($log->created_at) ? $log->created_at->format('Y-m-d h:i a') : ''
                ]);
            });
        });
        return $this->success('Successful', ['logs' => $logs], 200, true);
    }

    public function generateBanglaLog($field, $log, array $identifier) : string
    {
        $old_field = $this->objectToArray($log->old_value)[$field];
        $new_field = $this->objectToArray($log->new_value)[$field];

        $old_value = is_numeric($old_field) ? convertNumbersToBangla($old_field) : convertNumbersToBangla(0);
        $new_value = is_numeric($new_field) ? convertNumbersToBangla($new_field) : convertNumbersToBangla(0);

        switch ($field) {
            case FieldType::STOCK:
            case FieldType::VAT:
                $log = "$old_value $identifier[$field] ???????????? $new_value $identifier[$field]";
                break;
            case FieldType::PRICE:
                $log = "$identifier[$field] $old_value ???????????? $identifier[$field] $new_value";
                break;
            case FieldType::SUB_CATEGORY_ID:
                $sub_category_name = $this->getCategoryName($old_field, $new_field);
                $sub_category_name_old = $sub_category_name[0]['name'] ?? '';
                $sub_category_name_new = $sub_category_name[1]['name'] ?? '';
                $log = "$identifier[$field] $sub_category_name_old ???????????? $sub_category_name_new";
                break;
            case FieldType::CATEGORY_ID:
                $category_name = $this->getCategoryName($old_field, $new_field);
                $category_name_old = $category_name[0]['name'] ?? '';
                $category_name_new = $category_name[1]['name'] ?? '';
                $log = "$identifier[$field] $category_name_old ???????????? $category_name_new";
                break;
            case FieldType::UNIT:
                $unit_name_old = $old_field['name_bn'];
                $unit_name_new = $new_field['name_bn'];
                $log = "$identifier[$field] $unit_name_old ???????????? $unit_name_new";
                break;
            case FieldType::WARRANTY:
                $log = "$identifier[$field] $old_field ????????? ???????????? $new_field";
                break;
            case FieldType::NAME || FieldType::WARRANTY_UNIT || FieldType::APP_THUMB:
                $log = "$identifier[$field] $old_field ???????????? $new_field";
                break;
            default:
                $log = "{$old_field} ???????????? {$new_field}";
        }
        return $log;
    }

    private function objectToArray($object) : array {
        $old = json_decode($object);
        return json_decode(json_encode($old), true);
    }

    private function getCategoryName($old_field, $new_field) : Collection
    {
        return $this->categoryRepository->whereIn('id', [$old_field, $new_field])->get('name');
    }
}

<?php namespace App\Services\Product;

use App\Exceptions\ProductNotFoundException;
use App\Http\Resources\ProductsInfoResource;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Repositories\SkuBatchRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductList
{
    protected CategoryRepositoryInterface $categoryRepository;
    protected ProductRepositoryInterface $productRepository;
    protected int $partnerId;
    protected ?array $categoryIds;
    protected ?array $subCategoryIds;
    protected ?string $updatedAfter;
    protected ?int $offset;
    protected ?int $limit;
    protected ?int $webstorePublicationStatus;

    public function __construct(CategoryRepositoryInterface $categoryRepository,
                                ProductRepositoryInterface $productRepository,
                                protected SkuBatchRepository $skuBatchRepository,
                                protected SkuRepositoryInterface $skuRepository,
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param int $partnerId
     * @return $this
     */
    public function setPartnerId(int $partnerId): ProductList
    {
        $this->partnerId = $partnerId;
        return $this;
    }

    /**
     * @param array|null $categoryIds
     * @return ProductList
     */
    public function setCategoryIds(?array $categoryIds): ProductList
    {
        $this->categoryIds = $categoryIds;
        return $this;
    }

    /**
     * @param int|null $limit
     * @return $this
     */
    public function setLimit(?int $limit): ProductList
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param int|null $offset
     * @return $this
     */
    public function setOffset(?int $offset): ProductList
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @param array|null $subCategoryIds
     * @return ProductList
     */
    public function setSubCategoryIds(?array $subCategoryIds): ProductList
    {
        $this->subCategoryIds = $subCategoryIds;
        return $this;
    }

    /**
     * @param string|null $updatedAfter
     * @return $this
     */
    public function setUpdatedAfter(?string $updatedAfter): ProductList
    {
        $this->updatedAfter = $updatedAfter;
        return $this;
    }

    /**
     * @param int|null $webstorePublicationStatus
     * @return $this
     */
    public function setWebstorePublicationStatus(?int $webstorePublicationStatus): ProductList
    {
        $this->webstorePublicationStatus = $webstorePublicationStatus;
        return $this;
    }

    private function getProducts()
    {
        $products_query = $this->productRepository->where('partner_id', $this->partnerId)->with([
            'unit' => function($q) {
            $q->select('id', 'name_bn', 'name_en');
            }, 'category' => function($q) {
            $q->select('id', 'parent_id')->with(['parent' => function($q) {
                $q->select('id');
            }]);
            }, 'skus' => function($q) {
            $q->with(['batch', 'combinations' => function($q) {
                $q->with(['productOptionValue' => function($q) {
                    $q->with('productOption');
                }]);
            }, 'skuChannels' => function($q) {
                $q->with(['validDiscounts', 'sku' => function($q) {
                    $q->with('product');
                }]);
            }]);
        }]);
        if (isset($this->categoryIds)) $products_query = $products_query->whereIn('category_id', $this->categoryRepository->getSubCategoryIds($this->categoryIds)->pluck('id'));
        if (isset($this->setSubCategoryIds))
            $products_query = $this->filterBySubCategories($products_query, $this->setSubCategoryIds);
        if (isset($this->updatedAfter)) $products_query = $this->filterByUpdatedAfter($products_query, $this->updatedAfter);
        if (isset($this->webstorePublicationStatus))
            $products_query = $this->filterByWebstorePublicationStatus($products_query, $this->webstorePublicationStatus);
        return $products_query->offset($this->offset)->limit($this->limit)->get();
    }

    private function getDeletedProducts()
    {
        $deleted_products_query = $this->productRepository->where('partner_id', $this->partnerId)->onlyTrashed();
        if (isset($this->updatedAfter))
            $deleted_products_query = $deleted_products_query->where('deleted_at', '>=', $this->updatedAfter);
        return $deleted_products_query->select('id')->get();
    }

    /**
     * @return ProductsInfoResource
     * @throws ProductNotFoundException
     */
    public function get(): ProductsInfoResource
    {
        $products = $this->getProducts();
        $additional_data = $this->getPartnerProductsAdditionalInfo();
        $deleted_products = isset($this->updatedAfter) ? $this->getDeletedProducts() : collect([]);
        if ($products->isEmpty() & $deleted_products->isEmpty())
            throw new ProductNotFoundException('স্টকে কোন পণ্য নেই! প্রয়োজনীয় তথ্য দিয়ে স্টকে পণ্য যোগ করুন।');
        $products_with_deleted_products = collect([]);
        $products_with_deleted_products->products = $products;
        $products_with_deleted_products->deleted_products = $deleted_products;
        $products_with_deleted_products->total_products = $additional_data['total_products'];
        $products_with_deleted_products->total_products_with_variation = $additional_data['total_products_with_variation'];
        $products_with_deleted_products->total_buying_price = $additional_data['total_buying_price'];
        $products_with_deleted_products->items_with_buying_price = $additional_data['items_with_buying_price'];
        return new ProductsInfoResource($products_with_deleted_products);
    }


    private function filterBySubCategories($products_query, $subCategoryIds)
    {
        return $products_query->whereIn('category_id', $subCategoryIds);
    }

    private function filterByUpdatedAfter($products_query, $updatedAfter)
    {
        return $products_query->where(function ($q) use ($updatedAfter) {
            $q->where('updated_at', '>=', $updatedAfter);
            $q->orWhere('created_at', '>=', $updatedAfter);
        });
    }

    private function filterByWebstorePublicationStatus($products_query, $webstorePublicationStatus)
    {
        return $products_query->whereHas('productChannels', function ($query) use ($webstorePublicationStatus) {
            $query->whereHas('channel', function ($q) use ($webstorePublicationStatus) {
                $q->where('name', 'webstore');
                $q->where('is_published', $webstorePublicationStatus);
            });
        });
    }

    private function getPartnerProductsAdditionalInfo()
    {
        $return_data = [
            'total_products' => 0,
            'total_products_with_variation' => 0,
            'items_with_buying_price' => 0,
            'total_buying_price' => 0,
        ];
        $items = $this->productRepository->where('partner_id', $this->partnerId)->select('id')->get();
        $return_data['total_products'] += $items->count();
        $skus_ids = $this->skuRepository->whereIn('product_id', $items)->select('id')->get();
        $return_data['total_products_with_variation'] = $skus_ids->count();
        /** @var Collection $batches */
        $batches = $this->skuBatchRepository->whereIn('sku_id', $skus_ids)->where('cost', '>', 0)->get()->groupBy('sku_id');
        if ($batches) {
            $return_data['items_with_buying_price'] = $batches->count();
            foreach ($batches as $each) {
                $return_data['total_buying_price'] += $each->last()->cost;
            }
        }
        return $return_data;
    }
}

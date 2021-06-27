<?php namespace App\Services\Product;

use App\Exceptions\ProductNotFoundException;
use App\Http\Resources\ProductsInfoResource;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;

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

    public function __construct(CategoryRepositoryInterface $categoryRepository, ProductRepositoryInterface $productRepository)
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
        $products_query = $this->productRepository->where('partner_id', $this->partnerId);
        if (isset($this->categoryIds)) $products_query = $this->filterByCategories($products_query, $this->categoryIds);
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
        if ($products->isEmpty())
            throw new ProductNotFoundException('স্টকে কোন পণ্য নেই! প্রয়োজনীয় তথ্য দিয়ে স্টকে পণ্য যোগ করুন।');
        $deleted_products = isset($this->updatedAfter) ? $this->getDeletedProducts() : [];
        $products_with_deleted_products = collect([]);
        $products_with_deleted_products->products = $products;
        $products_with_deleted_products->deleted_products = $deleted_products;
        return new ProductsInfoResource($products_with_deleted_products);
    }

    private function filterByCategories($products_query, $categoryIds)
    {
        $subCategoryIds = collect([]);
        foreach ($categoryIds as $categoryId) {
            $category = $this->categoryRepository->find($categoryId);
            $children = $category->children()->pluck('id');
            if (!$children->isEmpty()) $subCategoryIds->push($children);
            $subCategoryIds->push($category->id);
        }
        return $products_query->whereIn('category_id', $subCategoryIds);
    }

    private function filterBySubCategories($products_query, $subCategoryIds)
    {
        return $products_query->whereIn('category_id', $subCategoryIds);
    }

    private function filterByUpdatedAfter($products_query, $updatedAfter)
    {
        return $products_query->where(function ($q) use ($updatedAfter) {
            $q->where('updated_at', '>=', $updatedAfter);
            $q->orWhere('created_at', '>=',$updatedAfter);
        });
    }

    private function filterByWebstorePublicationStatus($products_query, $webstorePublicationStatus)
    {
        return $products_query->whereHas('productChannels', function ($query) use ($webstorePublicationStatus) {
            $query->whereHas('channel', function ($q) use ($webstorePublicationStatus){
                $q->where('name', 'webstore');
                $q->where('is_published', $webstorePublicationStatus);
            });
        });
    }
}

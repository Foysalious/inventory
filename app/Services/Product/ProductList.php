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
    protected $categoryIds;
    protected $subCategoryIds;
    protected $updatedAfter;
    protected $offset;
    protected $limit;
    protected $webstorePublicationStatus;


    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        ProductRepositoryInterface $productRepository
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param mixed $partnerId
     * @return ProductList
     */
    public function setPartnerId($partnerId)
    {
        $this->partnerId = $partnerId;
        return $this;
    }

    /**
     * @param $categoryIds
     * @return $this
     */
    public function setCategoryIds($categoryIds)
    {
        $this->categoryIds = $categoryIds;
        return $this;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param mixed $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @param $subCategoryIds
     * @return ProductList
     */
    public function setSubCategoryIds($subCategoryIds)
    {
        $this->subCategoryIds = $subCategoryIds;
        return $this;
    }

    /**
     * @param mixed $updatedAfter
     * @return ProductList
     */
    public function setUpdatedAfter($updatedAfter)
    {
        $this->updatedAfter = $updatedAfter;
        return $this;
    }

    /**
     * @param mixed $webstorePublicationStatus
     * @return ProductList
     */
    public function setWebstorePublicationStatus($webstorePublicationStatus)
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
    public function get()
    {
        $products = $this->getProducts();
        if ($products->isEmpty())
            throw new ProductNotFoundException('স্টকে কোন পণ্য নেই! প্রয়োজনীয় তথ্য দিয়ে স্টকে পণ্য যোগ করুন।');
        $deleted_products = $this->getDeletedProducts();
        $products_with_deleted_products = collect([]);
        $products_with_deleted_products->products = $products;
        $products_with_deleted_products->deleted_products = $deleted_products;
        return new ProductsInfoResource($products_with_deleted_products);
    }

    private function filterByCategories($products_query, $categoryIds)
    {
        $subCategoryIds = collect([]);
        foreach ($this->categoryIds as $categoryId) {
            $category = $this->categoryRepository->find($categoryId);
            $subCategoryIds->push($category->children()->pluck('id'));
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

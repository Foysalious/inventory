<?php namespace App\Services\Webstore\Product;

use App\Exceptions\ProductNotFoundException;
use App\Http\Resources\ProductsInfoResource;
use App\Http\Resources\Webstore\ProductsResource;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductList
{
    protected CategoryRepositoryInterface $categoryRepository;
    protected ProductRepositoryInterface $productRepository;
    protected int $partnerId;
    protected $categoryIds;
    protected $subCategoryIds;
    protected $collectionIds;
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

//        if (isset($this->categoryIds)) $products_query = $this->filterByCategories($products_query, $this->categoryIds);
//        if (isset($this->setSubCategoryIds))
//            $products_query = $this->filterBySubCategories($products_query, $this->setSubCategoryIds);
//        $this->collectionIds = [57,69];
//        $products_query = $this->filterByCollectionIds($products_query, $this->collectionIds);
        return $products_query->get();
    }


    public function setCollectionIds($collectionIds)
    {
        $this->collectionIds = $collectionIds;
        return $this;
    }

    private function getDeletedProducts()
    {
        $deleted_products_query = $this->productRepository->where('partner_id', $this->partnerId)->onlyTrashed();
        if (isset($this->updatedAfter))
            $deleted_products_query = $deleted_products_query->where('deleted_at', '>=', $this->updatedAfter);
        return $deleted_products_query->select('id')->get();
    }


    public function get()
    {
        $products = $this->getProducts();
        if ($products->isEmpty())
            throw new ProductNotFoundException('স্টকে কোন পণ্য নেই! প্রয়োজনীয় তথ্য দিয়ে স্টকে পণ্য যোগ করুন।');
        return  ProductsResource::collection($products);
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

    private function filterByCollectionIds($products_query, $collectionIds)
    {
        return $products_query->whereHas('collections',function($q) use ($collectionIds){
            $q->whereIn('id', $collectionIds);
        });
    }

 /*   private function filterByPrice($products_query,$price_range)
    {
        return $products_query->
    }*/




}

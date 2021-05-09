<?php


namespace App\Sheba\Product;

use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use phpDocumentor\Reflection\Types\Boolean;

class ProductListWithCategory
{
    protected CategoryRepositoryInterface $categoryRepository;
    protected ProductRepositoryInterface $productRepository;

    protected int $partnerId;
    protected array $masterCatgoryIds;
    protected array $categoryIds;
    protected string $updatedAfter;
    protected int $offset;
    protected int $limit;
    protected bool $webStorePublicationStatus;


    /**
     * ProductListWithCategory constructor.
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository, ProductRepositoryInterface $productRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param mixed $partnerId
     */
    public function setPartnerId($partnerId)
    {
        $this->partnerId = $partnerId;
        return $this;
    }

    /**
     * @param mixed $masterCatgoryIds
     */
    public function setMasterCatgoryIds($masterCatgoryIds)
    {
        $this->masterCatgoryIds = json_decode($masterCatgoryIds);
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
     * @param mixed $categoryIds
     */
    public function setCategoryIds($categoryIds)
    {
        $this->categoryIds = json_decode($categoryIds);
        return $this;
    }

    /**
     * @param mixed $updatedAfter
     */
    public function setUpdatedAfter($updatedAfter)
    {
        $this->updatedAfter = $updatedAfter;
        return $this;
    }

    /**
     * @param mixed $webstorePublicationStatus
     */
    public function setWebstorePublicationStatus($webstorePublicationStatus)
    {
        $this->webStorePublicationStatus = $webstorePublicationStatus;
        return $this;
    }

    public function get()
    {
        $products = $this->productRepository->where('partner_id', $this->partnerId);
        $deleted_products = $this->productRepository->where('partner_id', $this->partnerId)->onlyTrashed();
        if (isset($this->masterCatgoryIds)) {
            $category_ids = collect([]);
            foreach ($this->masterCatgoryIds as $master_category_id) {
                $category = $this->categoryRepository->find($master_category_id);
                $category_ids->push($category->children()->pluck('id'));
                $category_ids->push($category->id);
            }
            $products = $products->whereIn('category_id', $category_ids);
        }
        if (isset($this->categoryIds)) {
            $products = $products->whereIn('category_id', $this->categoryIds);
        }
        if (isset($this->updatedAfter)) {
            $products = $products->where(function ($q) {
                $q->where('updated_at', '>=', $this->updatedAfter);
                $q->orWhere('created_at', '>=',$this->updatedAfter);
            });
            $deleted_products = $deleted_products->where('deleted_at', '>=', $this->updatedAfter);
        }
        if (isset($this->webStorePublicationStatus)) {
            $products = $products->whereHas('productChannels', function ($query) {
                $query->whereHas('channel', function ($q){
                    $q->where('name', 'webstore');
                    $q->where('is_published', $this->webStorePublicationStatus);
                });
            });
        }
        $products = $products->offset($this->offset)->limit($this->limit)->get();
        $deleted_products = $deleted_products->select('id')->get();
        return array_merge(['products' => $products, 'deleted_products' => $deleted_products]);
    }
}

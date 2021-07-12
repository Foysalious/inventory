<?php namespace App\Services\Webstore\Product;

use App\Exceptions\ProductNotFoundException;
use App\Http\Resources\ProductsInfoResource;
use App\Http\Resources\Webstore\ProductsResource;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Services\Channel\Channels;
use App\Services\Webstore\PosOrderServerClient;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

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
    protected $productCount;
    protected $priceRange;
    protected $ratings;
    protected PosOrderServerClient $posServerClient;


    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        ProductRepositoryInterface $productRepository,
        PosOrderServerClient $posServerClient
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->posServerClient = $posServerClient;

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


    public function setCollectionIds($collectionIds)
    {
        $this->collectionIds = $collectionIds;
        return $this;
    }

    public function setPriceRange($priceRange)
    {
        $this->priceRange = $priceRange;
        return $this;
    }

    public function setRatings($ratings)
    {
        $this->ratings = $ratings;
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

    private function getProducts()
    {
        $products_query = $this->productRepository->where('partner_id', $this->partnerId)
            ->whereHas('skus', function ($q) {
                $q->whereHas('batch', function ($q) {
                    $q->select(DB::raw('SUM(stock) as total_stock'))
                        ->havingRaw('total_stock > 0');
                });
            })->whereHas('skuChannels', function ($q) {
                $q->where('channel_id', Channels::WEBSTORE);
            });

        $this->productCount = $products_query->count();
        if (isset($this->categoryIds))
            $products_query = $this->filterByCategories($products_query, $this->categoryIds);
        if(isset($this->collectionIds))
            $products_query = $this->filterByCollectionIds($products_query, $this->collectionIds);
        if(isset($this->priceRange))
            $products_query = $this->filterByPrice($products_query, $this->priceRange);
        if(isset($this->ratings))
            $products_query = $this->filterByRatings($products_query, $this->ratings);
        return $products_query->offset($this->offset)->limit($this->limit)->get();
    }

    /**
     * @throws ProductNotFoundException
     */
    public function get(): array
    {
        $products = $this->getProducts();
        if ($products->isEmpty())
            throw new ProductNotFoundException('স্টকে কোন পণ্য নেই! প্রয়োজনীয় তথ্য দিয়ে স্টকে পণ্য যোগ করুন।');
        return  [$this->productCount,ProductsResource::collection($products)];
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

    private function filterByCollectionIds($products_query, $collectionIds)
    {
        return $products_query->whereHas('collections',function($q) use ($collectionIds){
            $q->whereIn('id', $collectionIds);
        });
    }

    public function filterByPrice($products_query,$priceRange)
    {
        return $products_query->whereHas('skus',function($q) use ($priceRange){
            $q->whereHas('skuChannels',function($q) use($priceRange){
                $q->where('channel_id',Channels::WEBSTORE)->whereBetween('price',$priceRange);
            });
        });
    }

    public function filterByRatings($products_query,$ratings)
    {
        $products_by_ratings =  $this->posServerClient->get('api/v1/webstore/partners/'.$this->partnerId.'/products-by-ratings?ratings='. json_encode($ratings))['product_ids_by_ratings'];
        return $products_query->whereIn('id',$products_by_ratings);
    }

}

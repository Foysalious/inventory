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
    protected array $categoryIds;
    protected $collectionIds;
    protected $offset;
    protected $limit;
    protected $webstorePublicationStatus;
    protected $productCount;
    protected $priceRange;
    protected $ratings;
    protected $searchKey;
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
    public function setCategoryIds(array $categoryIds)
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


    public function setCollectionIds(array $collectionIds)
    {
        $this->collectionIds = $collectionIds;
        return $this;
    }

    public function setPriceRange(array $priceRange)
    {
        $this->priceRange = $priceRange;
        return $this;
    }

    public function setRatings(array $ratings)
    {
        $this->ratings = $ratings;
        return $this;
    }

    public function setSearchKey($searchKey)
    {
        $this->searchKey = $searchKey;
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
        if (!empty($this->categoryIds))
            $products_query->whereIn('category_id', $this->categoryRepository->getSubCategoryIds($this->categoryIds)->pluck('id'));
        if (!empty($this->collectionIds))
            $products_query = $this->filterByCollectionIds($products_query, $this->collectionIds);
        if (!empty($this->priceRange))
            $products_query = $this->filterByPrice($products_query, $this->priceRange);
        if (!empty($this->ratings))
            $products_query = $this->filterByRatings($products_query, $this->ratings);
        if($this->searchKey)
            $products_query = $this->filterBySearchKey($products_query, $this->searchKey);
        $this->productCount = $products_query->count();
        return $products_query->offset($this->offset)->limit($this->limit)->get();
    }

    /**
     * @throws ProductNotFoundException
     */
    public function get(): array
    {
        $products = $this->getProducts();
        if ($products->isEmpty())
            throw new ProductNotFoundException('No Products Found');
        return [$this->productCount, ProductsResource::collection($products)];
    }


    private function filterByCollectionIds($products_query, $collectionIds)
    {
        return $products_query->whereHas('collections', function ($q) use ($collectionIds) {
            $q->whereIn('id', $collectionIds);
        });
    }

    public function filterByPrice($products_query, $priceRange)
    {
        return $products_query->whereHas('skus', function ($q) use ($priceRange) {
            $q->whereHas('skuChannels', function ($q) use ($priceRange) {
                $q->where('channel_id', Channels::WEBSTORE)->whereBetween('price', $priceRange);
            });
        });
    }

    public function filterByRatings($products_query, $ratings)
    {
        $products_by_ratings = $this->posServerClient->get('api/v1/webstore/partners/' . $this->partnerId . '/products-by-ratings?ratings=' . json_encode($ratings))['product_ids_by_ratings'];
        return $products_query->whereIn('id', $products_by_ratings);
    }

    public function filterBySearchKey($products_query, $searchKey)
    {
        return $products_query->where(function ($q) use ($searchKey) {
            $q->where('name', 'LIKE', '%' . $searchKey . '%')
                ->orWhere('description', 'LIKE', '%' . $searchKey . '%');
        });
    }
}

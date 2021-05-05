<?php namespace App\Services\CategoryProduct;


use App\Exceptions\ProductNotFoundException;
use App\Http\Resources\CategoryProductResource;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\CategoryPartnerRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryProductService extends BaseService
{
    private CategoryRepositoryInterface $categoryRepository;
    private CategoryPartnerRepositoryInterface $categoryPartnerRepository;
    private ProductRepositoryInterface $productRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository, CategoryPartnerRepositoryInterface $partnerCategoryRepository, ProductRepositoryInterface $productRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->categoryPartnerRepository = $partnerCategoryRepository;
        $this->productRepository = $productRepository;
    }


    public function getProducts($partner_id, Request $request)
    {
        list($offset, $limit) = calculatePagination($request);
        $products = $this->productRepository->where('partner_id', $partner_id);
        $deleted_products = $this->productRepository->where('partner_id', $partner_id)->onlyTrashed();
        if ($request->has('master_category_ids')) {
            $master_category_ids = json_decode($request->master_category_ids);
            $category_ids = collect([]);
            foreach ($master_category_ids as $master_category_id) {
                $category = $this->categoryRepository->find($master_category_id);
                $category_ids->push($category->children()->pluck('id'));
                $category_ids->push($category->id);
            }
            $products = $products->whereIn('category_id', $category_ids);
        }
        if ($request->has('category_ids')) {
            $category_ids = json_decode($request->category_ids);
            $products = $products->whereIn('category_id', $category_ids);
        }
        if ($request->has('updated_after')) {
            $products = $products->where(function ($q) use ($request) {
                $q->where('updated_at', '>=', $request->updated_after);
                $q->orWhere('created_at', '>=', $request->updated_after);
            });
            $deleted_products = $deleted_products->where('deleted_at', '>=', $request->updated_after);
        }
        if ($request->has('is_published_for_webstore')) {
            $products = $this->filterByPublicationsStatus($products, $request);
        }
        $products = $products->offset($offset)->limit($limit)->get();
        $deleted_products = $deleted_products->select('id')->get();
        $request->merge(['products' => $products, 'deleted_products' => $deleted_products]);
        if ($request->products->isEmpty()) throw new ProductNotFoundException('স্টকে কোন পণ্য নেই! প্রয়োজনীয় তথ্য দিয়ে স্টকে পণ্য যোগ করুন।');
        $items = collect([]);
        $resource = new CategoryProductResource($items);
        return $this->success("Successful", ['category_products' => $resource]);
    }

    /**
     * @param $products
     * @param $request
     * @return \Illuminate\Support\Collection
     */
    private function filterByPublicationsStatus ($products, $request, $channel='webstore')
    {
        return $products->whereHas('productChannels', function ($query) use ($request,$channel) {
            $query->whereHas('channel', function ($q) use ($request, $channel){
                $q->where('name', $channel);
                $q->where('is_published', $request->is_published_for_webstore);
            });
        });
    }
}

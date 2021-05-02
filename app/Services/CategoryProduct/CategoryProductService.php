<?php namespace App\Services\CategoryProduct;


use App\Http\Resources\CategoryProductResource;
use App\Http\Resources\PosProductsResource;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\CategoryPartnerRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Category;
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
        $products = $this->productRepository->where('partner_id', $partner_id);
        if ($request->has('category_id')) $products = $products->where('category_id', $request->category_id);
        if ($request->has('updated_after')) {
            $products = $products->where(function ($q) use ($request) {
                $q->where('updated_at', '>=', $request->updated_after);
                $q->orWhere('created_at', '>=', $request->updated_after);
            });
        }
        $products = $products->get();
        $request->merge(['products' => $products]);
        $items = collect([]);
        $items->total_items = $products->count();
        $resource = new CategoryProductResource($items);
        return $this->success("Successful", ['category_products' => $resource]);
    }
}

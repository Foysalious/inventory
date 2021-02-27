<?php namespace App\Services\CategoryProduct;


use App\Http\Resources\CategoryProductResource;
use App\Http\Resources\CategoryResource;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\CategoryPartnerRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;

class CategoryProductService
{
    use ResponseAPI;

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

        $products = $this->productRepository->where('partner_id', $partner_id)->get();
        $master_categories = $this->categoryRepository->builder()->whereHas('children', function ($q) use ($products) {
            $q->whereIn('id', $products->pluck('category_id')->unique()->toArray());
        })->get();

        $resource = CategoryProductResource::collection($master_categories);
        $data = [];
        $data['categories'] = $resource;

        return $this->success("Successful", $data);
    }
}

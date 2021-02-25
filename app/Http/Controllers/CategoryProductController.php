<?php namespace App\Http\Controllers;


use App\Services\Category\CategoryProductService;
use Illuminate\Http\Request;

class CategoryProductController extends Controller
{
//    private CategoryProductService $categoryProductService;
//
//    public function __construct(CategoryProductService $categoryProductService)
//    {
//        $this->categoryProductService = $categoryProductService;
//    }

    public function getProducts($partner, Request $request,CategoryProductService $categoryProductService)
    {
        $categoryProductService->getProducts($partner, $request);
    }
}

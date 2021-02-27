<?php namespace App\Http\Controllers;



use App\Services\CategoryProduct\CategoryProductService;
use Illuminate\Http\Request;

class CategoryProductController extends Controller
{

    public function getProducts($partner, Request $request,CategoryProductService $categoryProductService)
    {
        $categoryProductService->getProducts($partner, $request);
    }
}

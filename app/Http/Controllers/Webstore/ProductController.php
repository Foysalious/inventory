<?php namespace App\Http\Controllers\Webstore;

use App\Http\Controllers\Controller;
use App\Services\Webstore\Product\ProductService;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    public function search(Request $request, ProductService $productService)
    {
        $this->validate($request, ['search' => 'required|string', 'partner_id' => 'required|numeric']);
        return $productService->search($request->searchKey, +$request->partner_id);
    }

    public function getProductInformation(Request $request, $partner_id, $product_id, ProductService $productService)
    {
        return $productService->getProductInformation($request, $partner_id, $product_id);
    }

}

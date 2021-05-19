<?php namespace App\Http\Controllers\Webstore;

use App\Http\Controllers\Controller;
use App\Services\Webstore\Product\ProductService;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    public function search(Request $request ,$partner_id, ProductService $productService)
    {
        $this->validate($request, ['search' => 'required|string']);
        return $productService->search($request->search, $partner_id);
    }

}

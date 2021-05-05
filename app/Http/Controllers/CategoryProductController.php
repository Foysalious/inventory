<?php namespace App\Http\Controllers;



use App\Services\CategoryProduct\CategoryProductService;
use Illuminate\Http\Request;

class CategoryProductController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/partners/{partner}/category-products",
     *      operationId="Get Prodcut Stock List",
     *      tags={"Product Stock API"},
     *      summary="Apis to get products' stock related data",
     *      description="Return products' stock related data",
     *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="master_category_ids", description="master category ids", required=false, in="query", @OA\Schema(type="array", @OA\Items(type="integer")) ),
     *      @OA\Parameter(name="category_ids", description="category ids", required=false, in="query", @OA\Schema(type="array", @OA\Items(type="integer")) ),
     *      @OA\Parameter(name="updated_after", description="products updated after date", required=false, in="query", @OA\Schema(type="string") ),
     *      @OA\Parameter(name="offset", description="pagination offset", required=false, in="query", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="limit", description="pagination limit", required=false, in="query", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="is_published_for_webstore", description="publication filter for webstore", required=false, in="query", @OA\Schema(type="integer", enum={0,1}) ),
     *      @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="")),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Forbidden")
     *     )
     *
     * @param $partner
     * @param Request $request
     * @param CategoryProductService $categoryProductService
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ProductNotFoundException
     */
    public function getProducts($partner, Request $request,CategoryProductService $categoryProductService)
    {
       return $categoryProductService->getProducts($partner, $request);
    }
}

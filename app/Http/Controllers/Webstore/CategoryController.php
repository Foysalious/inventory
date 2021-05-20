<?php namespace App\Http\Controllers\Webstore;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Webstore\Cateogry\CategoryService;

class CategoryController extends Controller
{
    /**
     * @var CategoryService
     */
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    /**
     *
     * * @OA\Get(
     *      path="/api/v1/partners/{partner}/category",
     *      operationId="getCategory,
     *      tags={"Partners Category API"},
     *      summary="Get Category By Partner ID",
     *      description="",
     *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="")),
     *     )
     * @param $partner_id
     * @return JsonResponse
     */
    public function getAllCategory($partner_id)
    {
        return $this->categoryService->getCategoriesByPartner($partner_id);
    }

}

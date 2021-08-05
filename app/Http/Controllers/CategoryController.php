<?php namespace App\Http\Controllers;


use App\Exceptions\CategoryNotFoundException;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\CategoryWithSubCategory;
use App\Models\Category;
use App\Services\Category\CategoryService;
use App\Traits\ModificationFields;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ModificationFields;
    /**
     * @var CategoryService
     */
    protected CategoryService $categoryService;

    /**
     * CategoryController constructor.
     * @param CategoryService $categoryService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }


    /**
     *
     * * @OA\Get(
     *      path="/api/v1/partners/{partner}/category-tree",
     *      operationId="getCategory",
     *      tags={"Partners Category API"},
     *      summary="Get Category Tree List by Partner",
     *      description="",
     *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="")),
     *      @OA\Response(response=404, description="message: কোন ক্যাটাগরি যোগ করা হয়নি!"),
     *      @OA\Response(response=403, description="Forbidden")
     *     )
     * @param $partner_id
     * @param Request $request
     * @return JsonResponse
     * @throws CategoryNotFoundException
     */
    public function index($partner_id,Request $request): JsonResponse
    {
        return $this->categoryService->getCategoriesByPartner($partner_id, $request);
    }

    public function getCategoryProduct($category_id,Request $request)
    {
        return $this->categoryService->getCategoryByID($category_id,$request);

    }

    /**
     * @param CategoryRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/api/v1/partners/37900/categories",
     *      operationId="createCategory",
     *      tags={"Partners Category API"},
     *      summary="To create a category",
     *      description="creating partners category",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\Schema (ref="{}")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function store($partner,CategoryRequest $request)
    {
        return $this->categoryService->create($request, $partner);

    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @OA\Put (
     *      path="/api/v1/partners/37900/categories/10091",
     *      operationId="updatingcategory",
     *      tags={"Partners Category API"},
     *      summary="To update a category",
     *      description="updating partner's category",
     *      @OA\Parameter (
     *          name="id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *          type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="")
     *       ),
     *     @OA\Response(
     *          response=404,
     *          description="Category not found",
     *          @OA\JsonContent(ref="")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */

    public function update($partner, $category,CategoryRequest $request)
    {
        return $this->categoryService->update($request,$partner, $category);
    }

    public function destroy($partner, $category, Request $request)
    {
        return $this->categoryService->delete($partner,$request);
    }


    public function createCategoryWithSubCategory($partner, CategoryWithSubCategory $request)
    {
        return $this->categoryService->createCategoryWithSubCategory($request, $partner);
    }
}

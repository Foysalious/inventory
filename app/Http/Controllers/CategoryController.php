<?php namespace App\Http\Controllers;


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
     * @param $partner_id
     * @return JsonResponse
     */
    public function index($partner_id)
    {
        return $this->categoryService->getCategoriesByPartner($partner_id);
    }

    public function getCategoryProduct($category_id,Request $request)
    {
        return $this->categoryService->getCategoryByID($category_id,$request);

    }

    public function getMasterSubCat($partner_id)
    {
        return $this->categoryService->getCategory($partner_id);
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
     * @return \Illuminate\Http\JsonResponse
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

    public function destroy($partner, $category,Request $request)
    {
        return $this->categoryService->delete($request);
    }

    /**
     * @OA\POST(
     *      path="/api/v1/partners/{partner}/category-with-sub-category",
     *      operationId="Get Prodcut Stock List",
     *      tags={"Partners Category API"},
     *      summary="Creating a category with multiple sub-categories",
     *      description="",
     *     @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\MediaType(mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="category_name", type="string", @OA\Items(type="string")),
     *                  @OA\Property(property="category_thumb", type="file", @OA\Items(type="file")),
     *                  @OA\Property(property="sub_category[0][name]", type="string", collectionFormat="multi", @OA\Items(type="string")),
     *                  @OA\Property(property="sub_category[0][thumb]", type="file", collectionFormat="multi", @OA\Items(type="file")),
     *                  required={"category_name", "category_thumb", "sub_category[0][thumb]", "sub_category[0][name]" }
     *             )
     *         )
     *      ),
     *      @OA\Response(response=200, description="Successful", @OA\JsonContent(ref="")),
     *      @OA\Response(response=401, description="Unauthenticated"),
     *      @OA\Response(response=403, description="Forbidden")
     *     )
     *
     * @param $partner
     * @param CategoryWithSubCategory $request
     * @return JsonResponse
     */

    public function createCategoryWithSubCategory($partner, CategoryWithSubCategory $request)
    {
        return $this->categoryService->createCategoryWithSubCategory($request, $partner);
    }
}

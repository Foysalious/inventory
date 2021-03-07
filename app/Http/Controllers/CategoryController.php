<?php namespace App\Http\Controllers;


use App\Http\Requests\CategoryRequest;
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

    /**
     * @param CategoryRequest $request
     * @return JsonResponse
     */
    public function store($partner,CategoryRequest $request)
    {
        return $this->categoryService->create($request,$partner);
    }

    public function update($partner, $category,CategoryRequest $request)
    {
        return $this->categoryService->update($request,$partner, $category);
    }

    public function destroy($partner, $category,Request $request)
    {
        return $this->categoryService->delete($request);
    }

    public function getMasterSubCat(Request $request)
    {

        return $this->categoryService->getCategory();

    }



}

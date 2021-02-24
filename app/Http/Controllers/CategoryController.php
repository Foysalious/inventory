<?php namespace App\Http\Controllers;


use App\Http\Requests\CategoryRequest;
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
    public function store(CategoryRequest $request)
    {
        return $this->categoryService->create($request);
    }

    public function update(CategoryRequest $request, $partner_id, $category_id)
    {
        return $this->categoryService->update($request,$partner_id, $category_id);
    }

    public function delete(Request $request)
    {
        return $this->categoryService->delete($request);
    }
}

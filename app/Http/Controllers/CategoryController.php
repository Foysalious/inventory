<?php namespace App\Http\Controllers;


use App\Http\Requests\CategoryRequest;
use App\Services\Category\CategoryService;
use App\Traits\ModificationFields;
use Illuminate\Http\JsonResponse;

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
     * @param CategoryRequest $request
     * @return JsonResponse
     */
    public function store(CategoryRequest $request)
    {
        return $this->categoryService->create($request);
    }

    public function update(CategoryRequest $request, $category_id)
    {
        return $this->categoryService->update($request,$category_id);
    }



}

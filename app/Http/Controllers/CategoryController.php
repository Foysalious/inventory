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
        $this->setModifier($request->user);
        return $this->categoryService->create($request);
    }



}

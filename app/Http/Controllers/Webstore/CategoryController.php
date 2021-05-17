<?php namespace App\Http\Controllers\Webstore;

use App\Http\Controllers\Controller;
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

    public function getAllCategory($partner_id)
    {
        return $this->categoryService->getCategoriesByPartner($partner_id);
    }

}

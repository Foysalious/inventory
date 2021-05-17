<?php namespace App\Services\Webstore\Cateogry;


use App\Exceptions\CategoryNotFoundException;
use App\Interfaces\CategoryRepositoryInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;

class CategoryService
{
    use ResponseAPI;

    private CategoryRepositoryInterface $categoryRepositoryInterface;

    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
    }

    public function getCategoriesByPartner($partner_id)
    {
        $master_categories = $this->categoryRepositoryInterface->getMasterCategoryWebstore($partner_id);
        if ($master_categories->isEmpty())
            throw new CategoryNotFoundException('কোন ক্যাটাগরি যোগ করা হয়নি!');
        return $master_categories;
    }
}

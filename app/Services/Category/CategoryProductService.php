<?php namespace App\Services\Category;


use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\CategoryPartnerRepositoryInterface;
use App\Models\Category;
use App\Traits\ModificationFields;
use App\Traits\ResponseAPI;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CategoryProductService
{
    use ResponseAPI;

    private CategoryRepositoryInterface $categoryRepository;
    private CategoryPartnerRepositoryInterface $categoryPartnerRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository, CategoryPartnerRepositoryInterface $partnerCategoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->categoryPartnerRepository = $partnerCategoryRepository;
    }


    public function getProducts($partner_id, Request $request)
    {
        dd($this->categoryPartnerRepository->where('partner_id', $partner_id)->select('id', 'category_id')->get());
    }
}

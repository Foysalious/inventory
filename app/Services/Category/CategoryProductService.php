<?php namespace App\Services\Category;


use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\PartnerCategoryRepositoryInterface;
use App\Models\Category;
use App\Traits\ModificationFields;
use App\Traits\ResponseAPI;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CategoryProductService
{
    use ResponseAPI;

    private CategoryRepositoryInterface $categoryRepository;
    private PartnerCategoryRepositoryInterface $partnerCategoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository, PartnerCategoryRepositoryInterface $partnerCategoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->partnerCategoryRepository = $partnerCategoryRepository;
    }


    public function getProducts($partner_id, Request $request)
    {
        dd(Category::whereHas('partners', function (Builder $query) use ($partner_id) {
//            $query->where('id', 37900);
        })->get());
//        $this->partnerCategoryRepository->where('partner_id', $partner_id)->select('id', 'category_id')->get();
    }
}

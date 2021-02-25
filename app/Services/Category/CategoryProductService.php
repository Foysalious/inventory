<?php namespace App\Services\Category;


use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\PartnerCategoryRepositoryInterface;
use App\Traits\ModificationFields;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;

class CategoryProductService
{
    use ResponseAPI;

    use ModificationFields;
    protected CategoryRepositoryInterface $categoryRepositoryInterface;
    protected PartnerCategoryRepositoryInterface $partnerCategoryRepositoryInterface;
    protected string $categoryName;
    protected int $partnerId;
    protected $modifyBy;

    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface, PartnerCategoryRepositoryInterface $partnerCategoryRepositoryInterface)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->partnerCategoryRepositoryInterface = $partnerCategoryRepositoryInterface;
    }


    public function getProducts($partner_id, Request $request)
    {
        dd( $this->categoryRepositoryInterface);
//        dd($this->categoryRepository->builder()->whereHas('partners', function ($q) use ($partner_id) {
//            $q->select('id', $partner_id);
//        })->get());
//        $this->partnerCategoryRepository->where('partner_id', $partner_id)->select('id', 'category_id')->get();
    }
}

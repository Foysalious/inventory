<?php namespace App\Services\Category;

use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\PartnerCategoryRepositoryInterface;
use App\Traits\ModificationFields;

class Creator
{
    use ModificationFields;
    protected CategoryRepositoryInterface $categoryRepositoryInterface;
    protected PartnerCategoryRepositoryInterface $partnerCategoryRepositoryInterface;
    protected string $categoryName;
    protected int $partnerId;

    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface, PartnerCategoryRepositoryInterface $partnerCategoryRepositoryInterface)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->partnerCategoryRepositoryInterface = $partnerCategoryRepositoryInterface;
    }

    public function setPartner($partner_id)
    {
       $this->partnerId =  $partner_id;
       return $this;
    }

    public function setName($category_name)
    {
        $this->categoryName = $category_name;
        return $this;
    }

    public function create()
    {
        $master_category = $this->createMasterCategory();
        $sub_category = $this->createSubCategory($master_category->id);
        return  $this->createPartnerCategory($this->partnerId, $master_category->id, $sub_category->id);

    }

    public function createMasterCategory()
    {
        $master_category_data = [
            'parent_id' => null,
            'name' => $this->categoryName,
            'publication_status' => 1,
            'is_published_for_sheba' => 0,
        ];

       return  $this->categoryRepositoryInterface->create($master_category_data);
    }

    public function createSubCategory($master_category_id)
    {
        $sub_category_data = [
            'parent_id' => $master_category_id,
            'name' => 'Sub None Category',
            'publication_status' => 1,
            'is_published_for_sheba' => 0,
        ];
        return  $this->categoryRepositoryInterface->create($sub_category_data);

    }


    public function createPartnerCategory($partner_id, $master_category_id, $sub_category_id)
    {
        $sub_category_data = [
            [
                'partner_id' => $partner_id,
                'category_id' => $master_category_id,
            ] + $this->modificationFields(true, false),
            [
                'partner_id' => $partner_id,
                'category_id' => $sub_category_id,
            ] + $this->modificationFields(true, false)

        ];

        return $this->partnerCategoryRepositoryInterface->insert($sub_category_data);

    }




}

<?php


namespace App\Services\Category;


use App\Interfaces\CategoryPartnerRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Services\FileManagers\CdnFileManager;
use App\Services\FileManagers\FileManager;
use App\Traits\ModificationFields;

class CategoryWithSubCategoryCreator extends Creator
{
    use ModificationFields, FileManager, CdnFileManager;

    private array $partnerCategoryData;
    protected array $subCategory;

    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface, CategoryPartnerRepositoryInterface $partnerCategoryRepositoryInterface)
    {
       parent::__construct($categoryRepositoryInterface, $partnerCategoryRepositoryInterface);
    }


    /**
     * @param mixed $subCategory
     */
    public function setSubCategory($subCategory)
    {
        $this->subCategory = $subCategory;
        return $this;
    }

    public function create()
    {
        $category = $this->createMasterCategory();
        $this->addPartnerCategoryData($category->id);
        $default_sub_category = $this->createSubCategory($category->id);
        $this->addPartnerCategoryData($default_sub_category->id,true);
        $this->setParentId($category->id);
        $this->createSubCategories($category->id);
        return $this->createPartnerCategory();

    }

    private function addPartnerCategoryData($category_id, $default=false)
    {
        $this->partnerCategoryData [] = [
                'partner_id' => $this->partnerId,
                'category_id' => $category_id,
                'is_default' => $default,
            ] + $this->modificationFields(true, false);
    }

    private function createSubCategories($category_id)
    {
        foreach ($this->subCategory as $each) {
            $this->setName($each['name']);
            $this->setThumb($each['thumb'] ?? getCategoryDefaultThumb());
            $sub_category = $this->createSubCategory($category_id);
            $this->addPartnerCategoryData($sub_category->id);
        }
    }

    private function createPartnerCategory()
    {
        return $this->partnerCategoryRepositoryInterface->insert($this->partnerCategoryData);
    }


}

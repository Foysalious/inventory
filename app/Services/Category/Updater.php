<?php namespace App\Services\Category;


use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\CategoryPartnerRepositoryInterface;
use App\Models\Category;
use App\Traits\ModificationFields;

class Updater
{
    use ModificationFields;
    /**
     * @var CategoryRepositoryInterface
     */
    protected CategoryRepositoryInterface $categoryRepositoryInterface;
    /**
     * @var CategoryPartnerRepositoryInterface
     */
    protected CategoryPartnerRepositoryInterface $partnerCategoryRepositoryInterface;
    protected $name;
    /**
     * @var Category
     */
    protected Category $category;
    protected $modifyBy;

    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface, CategoryPartnerRepositoryInterface $partnerCategoryRepositoryInterface)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->partnerCategoryRepositoryInterface = $partnerCategoryRepositoryInterface;
    }
    public function setModifyBy($modify_by)
    {
        $this->modifyBy = $modify_by;
        return $this;
    }


    public function setCategory(Category $category): Updater
    {
        $this->category = $category;
        return $this;
    }

    public function setName($name): Updater
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function makeData()
    {
        return [
            'name' => $this->name
        ] + $this->modificationFields(false, true);
    }

    public function update()
    {
        $this->setModifier($this->modifyBy);
        return $this->categoryRepositoryInterface->update($this->category, $this->makeData());
    }


}

<?php namespace App\Services\DataMigration;


use App\Interfaces\CategoryPartnerRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;

class DataMigrationService
{
    private CategoryRepositoryInterface $categoryRepositoryInterface;
    private CategoryPartnerRepositoryInterface $categoryPartnerRepositoryInterface;
    private array $categoryPartner;
    private array $categories;

    /**
     * DataMigrationService constructor.
     * @param CategoryRepositoryInterface $categoryRepositoryInterface
     * @param CategoryPartnerRepositoryInterface $categoryPartnerRepositoryInterface
     */
    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface, CategoryPartnerRepositoryInterface $categoryPartnerRepositoryInterface)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->categoryPartnerRepositoryInterface = $categoryPartnerRepositoryInterface;
    }

    /**
     * @param $categoryPartner
     * @return DataMigrationService
     */
    public function setPartnerCategories($categoryPartner)
    {
        $this->categoryPartner = $categoryPartner;
        return $this;
    }

    /**
     * @param mixed $categories
     * @return DataMigrationService
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
        return $this;
    }

    public function migrate()
    {
        $this->migrateCategoryData();
    }

    private function migrateCategoryData()
    {
        $this->categoryRepositoryInterface->insertOrIgnore($this->categories);
        $this->categoryPartnerRepositoryInterface->insertOrIgnore($this->categoryPartner);
    }

}

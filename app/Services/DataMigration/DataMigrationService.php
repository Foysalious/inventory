<?php namespace App\Services\DataMigration;


use App\Interfaces\CategoryPartnerRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\DiscountRepositoryInterface;
use App\Interfaces\PartnerRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\ProductUpdateLogRepositoryInterface;
use App\Interfaces\UnitRepositoryInterface;

class DataMigrationService
{
    private CategoryRepositoryInterface $categoryRepositoryInterface;
    private CategoryPartnerRepositoryInterface $categoryPartnerRepositoryInterface;
    private PartnerRepositoryInterface $partnerRepositoryInterface;
    private $categoryPartner;
    private $categories;
    private $products;
    private $partnerInfo;
    /** @var ProductUpdateLogRepositoryInterface */
    private ProductUpdateLogRepositoryInterface $productUpdateLogRepositoryInterface;
    private $productUpdateLogs;
    /** @var ProductRepositoryInterface */
    private ProductRepositoryInterface $productRepositoryInterface;
    /** @var UnitRepositoryInterface */
    private UnitRepositoryInterface $unitRepositoryInterface;
    /** @var DiscountRepositoryInterface */
    private DiscountRepositoryInterface $discountRepositoryInterface;
    private $discounts;

    /**
     * DataMigrationService constructor.
     * @param CategoryRepositoryInterface $categoryRepositoryInterface
     * @param CategoryPartnerRepositoryInterface $categoryPartnerRepositoryInterface
     * @param PartnerRepositoryInterface $partnerRepositoryInterface
     * @param ProductUpdateLogRepositoryInterface $productUpdateLogRepositoryInterface
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param UnitRepositoryInterface $unitRepositoryInterface
     * @param DiscountRepositoryInterface $discountRepositoryInterface
     */
    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface,
                                CategoryPartnerRepositoryInterface $categoryPartnerRepositoryInterface,
                                PartnerRepositoryInterface $partnerRepositoryInterface, ProductUpdateLogRepositoryInterface $productUpdateLogRepositoryInterface,
                                ProductRepositoryInterface $productRepositoryInterface, UnitRepositoryInterface $unitRepositoryInterface,
                                DiscountRepositoryInterface $discountRepositoryInterface)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->categoryPartnerRepositoryInterface = $categoryPartnerRepositoryInterface;
        $this->partnerRepositoryInterface = $partnerRepositoryInterface;
        $this->productUpdateLogRepositoryInterface = $productUpdateLogRepositoryInterface;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->unitRepositoryInterface = $unitRepositoryInterface;
        $this->discountRepositoryInterface = $discountRepositoryInterface;
    }

    /**
     * @param $partnerInfo
     * @return DataMigrationService
     */
    public function setPartnerInfo($partnerInfo)
    {
        $this->partnerInfo = $partnerInfo;
        return $this;
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

    /**
     * @param array $products
     * @return DataMigrationService
     */
    public function setProducts($products)
    {
        $this->products = $products;
        return $this;
    }

    /**
     * @param mixed $productUpdateLogs
     * @return DataMigrationService
     */
    public function setProductUpdateLogs($productUpdateLogs)
    {
        $this->productUpdateLogs = $productUpdateLogs;
        return $this;
    }

    /**
     * @param mixed $discounts
     * @return DataMigrationService
     */
    public function setDiscounts($discounts)
    {
        $this->discounts = $discounts;
        return $this;
    }

    public function migrate()
    {
        $this->migratePartnerInfoData();
        $this->migrateCategoryData();
        $this->migrateProductsData();
        $this->migrateProductDiscountsData();
        $this->migrateProductUpdateLogsData();
    }

    private function migratePartnerInfoData()
    {
        $this->partnerRepositoryInterface->insertOrIgnore($this->partnerInfo);
    }

    private function migrateCategoryData()
    {
        $this->categoryRepositoryInterface->insertOrIgnore($this->categories);
        $this->categoryPartnerRepositoryInterface->insertOrIgnore($this->categoryPartner);
    }

    private function migrateProductsData()
    {
        $units = $this->unitRepositoryInterface->builder()->select('id', 'name_en')->pluck('name_en', 'id')->toArray();
        foreach ($this->products as $singleProduct)
        {
            $unit = array_search($singleProduct['unit'], $units, true);
            $this->productRepositoryInterface->insertOrIgnore([
                'id' => $singleProduct['id'],
                'partner_id' => $singleProduct['partner_id'],
                'category_id' => $singleProduct['category_id'],
                'name' => $singleProduct['name'],
                'description' => $singleProduct['description'],
                'unit_id' => $unit ?: null,
                'warranty' => $singleProduct['warranty'],
                'warranty_unit' => $singleProduct['warranty_unit'],
                'vat_percentage' => $singleProduct['vat_percentage'],
                'created_by_name' => $singleProduct['created_by_name'],
                'updated_by_name' => $singleProduct['updated_by_name'],
                'created_at' => $singleProduct['created_at'],
                'updated_at' => $singleProduct['updated_at'],
            ]);
            $product = $this->productRepositoryInterface->find($singleProduct['id']);
            if ($product && !$product->skus()->exists()) {
                $sku = $product->skus()->create([
                    'stock' => $singleProduct['stock'],
                    'created_by_name' => $singleProduct['created_by_name'],
                    'updated_by_name' => $singleProduct['updated_by_name'],
                    'created_at' => $singleProduct['created_at'],
                    'updated_at' => $singleProduct['updated_at'],
                ]);
                $sku_channels = collect();
                if ($singleProduct['publication_status']) $sku_channels->push([
                    'channel_id' => 1,
                    'cost' => $singleProduct['cost'],
                    'price' => $singleProduct['price'],
                    'wholesale_price' => $singleProduct['wholesale_price'],
                    'created_by_name' => $singleProduct['created_by_name'],
                    'updated_by_name' => $singleProduct['updated_by_name'],
                    'created_at' => $singleProduct['created_at'],
                    'updated_at' => $singleProduct['updated_at'],
                ]);
                if ($singleProduct['is_published_for_shop']) $sku_channels->push([
                    'channel_id' => 2,
                    'cost' => $singleProduct['cost'],
                    'price' => $singleProduct['price'],
                    'wholesale_price' => $singleProduct['wholesale_price'],
                    'created_by_name' => $singleProduct['created_by_name'],
                    'updated_by_name' => $singleProduct['updated_by_name'],
                    'created_at' => $singleProduct['created_at'],
                    'updated_at' => $singleProduct['updated_at'],
                ]);
                if ($singleProduct['publication_status']) $sku->skuChannels()->insertOrIgnore($sku_channels->toArray());
            }
        }
    }

    private function migrateProductUpdateLogsData()
    {
        $this->productUpdateLogRepositoryInterface->insertOrIgnore($this->productUpdateLogs);
    }

    private function migrateProductDiscountsData()
    {
        $this->discountRepositoryInterface->insertOrIgnore($this->discounts);
    }
}

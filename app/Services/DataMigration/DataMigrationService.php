<?php namespace App\Services\DataMigration;


use App\Interfaces\CategoryPartnerRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\ChannelRepositoryInterface;
use App\Interfaces\DiscountRepositoryInterface;
use App\Interfaces\PartnerRepositoryInterface;
use App\Interfaces\ProductImageRepositoryInterface;
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
    /** @var ChannelRepositoryInterface */
    private ChannelRepositoryInterface $channelRepositoryInterface;
    private $productImages;
    /** @var ProductImageRepositoryInterface */
    private ProductImageRepositoryInterface $productImageRepositoryInterface;

    /**
     * DataMigrationService constructor.
     * @param CategoryRepositoryInterface $categoryRepositoryInterface
     * @param CategoryPartnerRepositoryInterface $categoryPartnerRepositoryInterface
     * @param PartnerRepositoryInterface $partnerRepositoryInterface
     * @param ProductUpdateLogRepositoryInterface $productUpdateLogRepositoryInterface
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param UnitRepositoryInterface $unitRepositoryInterface
     * @param DiscountRepositoryInterface $discountRepositoryInterface
     * @param ChannelRepositoryInterface $channelRepositoryInterface
     * @param ProductImageRepositoryInterface $productImageRepositoryInterface
     */
    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface,
                                CategoryPartnerRepositoryInterface $categoryPartnerRepositoryInterface,
                                PartnerRepositoryInterface $partnerRepositoryInterface, ProductUpdateLogRepositoryInterface $productUpdateLogRepositoryInterface,
                                ProductRepositoryInterface $productRepositoryInterface, UnitRepositoryInterface $unitRepositoryInterface,
                                DiscountRepositoryInterface $discountRepositoryInterface,
                                ChannelRepositoryInterface $channelRepositoryInterface,
                                ProductImageRepositoryInterface $productImageRepositoryInterface)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->categoryPartnerRepositoryInterface = $categoryPartnerRepositoryInterface;
        $this->partnerRepositoryInterface = $partnerRepositoryInterface;
        $this->productUpdateLogRepositoryInterface = $productUpdateLogRepositoryInterface;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->unitRepositoryInterface = $unitRepositoryInterface;
        $this->discountRepositoryInterface = $discountRepositoryInterface;
        $this->channelRepositoryInterface = $channelRepositoryInterface;
        $this->productImageRepositoryInterface = $productImageRepositoryInterface;
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
     * @param $productImages
     * @return DataMigrationService
     */
    public function setProductImages($productImages)
    {
        $this->productImages = $productImages;
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
        if ($this->partnerInfo) $this->migratePartnerInfoData();
        if ($this->categories) $this->migrateCategoriesData();
        if ($this->categoryPartner) $this->migrateCategoryPartnerData();
        if ($this->products) $this->migrateProductsData();
        if ($this->productImages) $this->migrateProductImagesData();
        if ($this->productUpdateLogs) $this->migrateProductUpdateLogsData();
        if ($this->discounts) $this->migrateProductDiscountsData();
    }

    private function migratePartnerInfoData()
    {
        $this->partnerRepositoryInterface->insertOrIgnore($this->partnerInfo);
    }

    private function migrateCategoriesData()
    {
        $this->categoryRepositoryInterface->insertOrIgnore($this->categories);
    }

    private function migrateCategoryPartnerData()
    {
        $this->categoryPartnerRepositoryInterface->insertOrIgnore($this->categoryPartner);
    }

    private function migrateProductsData()
    {
        $units = $this->unitRepositoryInterface->builder()->select('id', 'name_en')->pluck('name_en', 'id')->toArray();
        $pos_channel_id = $this->channelRepositoryInterface->where('name', 'pos')->first();
        $webstore_channel_id = $this->channelRepositoryInterface->where('name', 'webstore')->first();
        foreach ($this->products as $singleProduct)
        {
            $unit = array_search($singleProduct['unit'], $units, true);
            $this->productRepositoryInterface->insertOrIgnore([
                'id' => $singleProduct['id'],
                'partner_id' => $singleProduct['partner_id'],
                'category_id' => $singleProduct['category_id'],
                'name' => $singleProduct['name'],
                'app_thumb' => $singleProduct['app_thumb'],
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
                    'channel_id' => $pos_channel_id,
                    'cost' => $singleProduct['cost'],
                    'price' => $singleProduct['price'],
                    'wholesale_price' => $singleProduct['wholesale_price'],
                    'created_by_name' => $singleProduct['created_by_name'],
                    'updated_by_name' => $singleProduct['updated_by_name'],
                    'created_at' => $singleProduct['created_at'],
                    'updated_at' => $singleProduct['updated_at'],
                ]);
                if ($singleProduct['is_published_for_shop']) $sku_channels->push([
                    'channel_id' => $webstore_channel_id,
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

    private function migrateProductImagesData()
    {
        $this->productImageRepositoryInterface->insertOrIgnore($this->productImages);
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

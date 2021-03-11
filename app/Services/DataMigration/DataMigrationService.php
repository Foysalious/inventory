<?php namespace App\Services\DataMigration;


use App\Interfaces\CategoryPartnerRepositoryInterface;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\PartnerRepositoryInterface;
use App\Models\Product;
use App\Models\Unit;

class DataMigrationService
{
    private CategoryRepositoryInterface $categoryRepositoryInterface;
    private CategoryPartnerRepositoryInterface $categoryPartnerRepositoryInterface;
    private PartnerRepositoryInterface $partnerRepositoryInterface;
    private array $categoryPartner;
    private array $categories;
    private $products;
    private $partner;

    /**
     * DataMigrationService constructor.
     * @param CategoryRepositoryInterface $categoryRepositoryInterface
     * @param CategoryPartnerRepositoryInterface $categoryPartnerRepositoryInterface
     */
    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface, CategoryPartnerRepositoryInterface $categoryPartnerRepositoryInterface, PartnerRepositoryInterface $partnerRepositoryInterface)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->categoryPartnerRepositoryInterface = $categoryPartnerRepositoryInterface;
        $this->partnerRepositoryInterface = $partnerRepositoryInterface;
    }

    /**
     * @param mixed $partner
     * @return DataMigrationService
     */
    public function setPartner($partner)
    {
        $this->partner = $partner;
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


    public function migrate()
    {
        $this->migratePartnerData();
        $this->migrateCategoryData();
        $this->migrateProductsData();
    }

    private function migratePartnerData()
    {
        $this->partnerRepositoryInterface->insertOrIgnore($this->partner);
    }

    private function migrateCategoryData()
    {
        $this->categoryRepositoryInterface->insertOrIgnore($this->categories);
        $this->categoryPartnerRepositoryInterface->insertOrIgnore($this->categoryPartner);
    }

    private function migrateProductsData()
    {
        $units = Unit::select('id', 'name_en')->pluck('name_en', 'id')->toArray();
        foreach ($this->products as $singleProduct)
        {
            $unit = array_search($singleProduct['unit'], $units, true);
            $data = [
                'id' => $singleProduct['id'],
                'partner_id' => $singleProduct['partner_id'],
                'category_id' => $singleProduct['category_id'],
                'name' => $singleProduct['name'],
                'description' => $singleProduct['description'],
                'unit' => $unit ?: null,
                'warranty' => $singleProduct['warranty'],
                'warranty_unit' => $singleProduct['warranty_unit'],
                'vat_percentage' => $singleProduct['vat_percentage'],
                'created_by_name' => $singleProduct['created_by_name'],
                'created_at' => $singleProduct['created_at'],
                'updated_at' => $singleProduct['updated_at'],
            ];

            $product = Product::insert([
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
                'created_at' => $singleProduct['created_at'],
                'updated_at' => $singleProduct['updated_at'],
            ]);
            $product = Product::find($singleProduct['id']);
            $sku = $product->skus()->create([
                'stock' => $singleProduct['stock']
            ]);
            $sku_channels = collect();
            if ($singleProduct['publication_status']) $sku_channels->push([
                'channel_id' => 1,
                'cost' => $singleProduct['cost'],
                'price' => $singleProduct['price'],
                'wholesale_price' => $singleProduct['wholesale_price'],
            ]);
            if ($singleProduct['is_published_for_shop']) $sku_channels->push([
                'channel_id' => 2,
                'cost' => $singleProduct['cost'],
                'price' => $singleProduct['price'],
                'wholesale_price' => $singleProduct['wholesale_price'],
            ]);
            if ($singleProduct['publication_status']) $sku->skuChannels()->insert($sku_channels->toArray());
        }
    }

}

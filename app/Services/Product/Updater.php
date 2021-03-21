<?php namespace App\Services\Product;


use App\Interfaces\CombinationRepositoryInterface;
use App\Interfaces\OptionRepositoryInterface;
use App\Interfaces\ProductChannelRepositoryInterface;
use App\Interfaces\ProductOptionRepositoryInterface;
use App\Interfaces\ProductOptionValueRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Interfaces\ValueRepositoryInterface;
use App\Models\Product;
use App\Models\Sku;
use App\Services\Discount\Creator as DiscountCreator;
use App\Services\Product\Update\Operations\OptionsChanged;
use App\Services\Product\Update\Operations\ValuesAdded;
use App\Services\Product\Update\Operations\ValuesAddedDeleted;
use App\Services\Product\Update\Operations\ValuesDeleted;
use App\Services\ProductImage\Creator as ProductImageCreator;

class Updater
{
    protected ProductRepositoryInterface $productRepositoryInterface;
    /** @var Product */
    protected Product $product;
    protected $partnerId;
    protected $categoryId;
    protected $name;
    protected $description;
    protected $warranty;
    protected $warrantyUnit;
    protected $vatPercentage;
    protected $unitId;
    protected $productDetails;
    protected $optionRepositoryInterface;
    protected $valueRepositoryInterface;
    protected $productOptionRepositoryInterface;
    protected $productOptionValueRepositoryInterface;
    protected $combinationRepositoryInterface;
    protected $productChannelRepositoryInterface;
    protected $skuRepositoryInterface;
    protected $discountAmount;
    protected $discountEndDate;
    protected $images;
    private $options;
    private $productUpdateRequestObjects;
    /**
     * @var mixed
     */
    private $deletedValues;


    /**
     * Updater constructor.
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param DiscountCreator $discountCreator
     * @param ProductImageCreator $productImageCreator
     * @param OptionRepositoryInterface $optionRepositoryInterface
     * @param ValueRepositoryInterface $valueRepositoryInterface
     * @param ProductOptionRepositoryInterface $productOptionRepositoryInterface
     * @param ProductOptionValueRepositoryInterface $productOptionValueRepositoryInterface
     * @param CombinationRepositoryInterface $combinationRepositoryInterface
     * @param ProductChannelRepositoryInterface $productChannelRepositoryInterface
     */
    public function __construct(ProductRepositoryInterface $productRepositoryInterface, DiscountCreator $discountCreator, ProductImageCreator $productImageCreator,
                                OptionRepositoryInterface $optionRepositoryInterface, ValueRepositoryInterface  $valueRepositoryInterface, ProductOptionRepositoryInterface $productOptionRepositoryInterface,
                                ProductOptionValueRepositoryInterface $productOptionValueRepositoryInterface, CombinationRepositoryInterface  $combinationRepositoryInterface,
                                ProductChannelRepositoryInterface $productChannelRepositoryInterface, SkuRepositoryInterface $skuRepositoryInterface)
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->productImageCreator = $productImageCreator;
        $this->discountCreator = $discountCreator;
        $this->optionRepositoryInterface = $optionRepositoryInterface;
        $this->valueRepositoryInterface = $valueRepositoryInterface;
        $this->combinationRepositoryInterface = $combinationRepositoryInterface;
        $this->productOptionRepositoryInterface = $productOptionRepositoryInterface;
        $this->productOptionValueRepositoryInterface = $productOptionValueRepositoryInterface;
        $this->productChannelRepositoryInterface =  $productChannelRepositoryInterface;
        $this->skuRepositoryInterface = $skuRepositoryInterface;
    }

    /**
     * @param Product $product
     * @return Updater
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @param mixed $categoryId
     * @return Updater
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    /**
     * @param mixed $name
     * @return Updater
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $description
     * @return Updater
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param mixed $warranty
     * @return Updater
     */
    public function setWarranty($warranty)
    {
        $this->warranty = $warranty;
        return $this;
    }

    /**
     * @param mixed $warrantyUnit
     * @return Updater
     */
    public function setWarrantyUnit($warrantyUnit)
    {
        $this->warrantyUnit = $warrantyUnit;
        return $this;
    }

    /**
     * @param mixed $vatPercentage
     * @return Updater
     */
    public function setVatPercentage($vatPercentage)
    {
        $this->vatPercentage = $vatPercentage;
        return $this;
    }

    /**
     * @param mixed $unitId
     * @return Updater
     */
    public function setUnitId($unitId)
    {
        $this->unitId = $unitId;
        return $this;
    }

    public function setDiscount($discount_amount)
    {
        $this->discountAmount = $discount_amount;
        return $this;
    }

    public function setDiscountEndDate($discount_end_date)
    {
        $this->discountEndDate = $discount_end_date;
        return $this;
    }

    public function setImages($images)
    {
        $this->images = $images;
        return $this;
    }

    public function setProductUpdateRequestObjects($productUpdateRequestObjects)
    {
        $this->productUpdateRequestObjects = $productUpdateRequestObjects;
        return $this;
    }

    public function setHasVariant($hasVariants)
    {
        $this->hasVariants = $hasVariants;
        return $this;
    }

    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    public function update()
    {
        $product = $this->productRepositoryInterface->update($this->product, $this->makeData());
        $nature =  $this->getNature($product, $this->productUpdateRequestObjects);
        if ($nature == UpdateNature::OPTIONS_CHANGED)
            return app(OptionsChanged::class)->setProduct($product)->setUpdatedDataObjects($this->productUpdateRequestObjects)->apply();
        elseif ($nature == UpdateNature::VALUE_ADD_DELETE)
            return app(ValuesAddedDeleted::class)->setProduct($product)->setDeletedValues($this->deletedValues)->setUpdatedDataObjects($this->productUpdateRequestObjects)->apply();
        elseif ($nature == UpdateNature::VALUE_ADD)
            return app(ValuesAdded::class)->setProduct($product)->setUpdatedDataObjects($this->productUpdateRequestObjects)->apply();
        else
            return app(ValuesDeleted::class)->setProduct($product)->setDeletedValues($this->deletedValues)->setUpdatedDataObjects($this->productUpdateRequestObjects)->apply();
    }

    private function getNature($product, $skus)
    {
        if ($this->checkIsOptionChanged($skus[0]->getCombination()))
            return UpdateNature::OPTIONS_CHANGED;
        list($is_new_values_added, $updatedValues) = $this->checkIsValuesAdded($skus);
        list($is_values_deleted,$deleted_values) = $this->checkIsValesDeleted($product, $updatedValues);
        $this->deletedValues = $deleted_values;

        if ($is_new_values_added && $is_values_deleted)
            return UpdateNature::VALUE_ADD_DELETE;
        elseif ($is_new_values_added && !$is_values_deleted)
            return UpdateNature::VALUE_ADD;
        else
            return UpdateNature::VALUE_DELETE;

    }

    private function checkIsValesDeleted($product, $updatedValues)
    {
        $is_deleted = false;
        $created_product_option_value_ids = $this->combinationRepositoryInterface->whereIn('sku_id', $product->skus()->pluck('id'));

        $filtered_updated_values = array_filter($updatedValues, function ($a) {
            return $a !== null;
        });
        if($created_product_option_value_ids != $filtered_updated_values)
            $is_deleted = true;

        return $is_deleted ? [$is_deleted,array_diff($created_product_option_value_ids,$filtered_updated_values)] : [false,null];


    }

    private function checkIsValuesAdded($skus)
    {
        $product_option_value_ids = [];
        foreach ($skus as $sku) {
            $combination = $sku->getCombination();
            foreach ($combination as $options_values) {
                foreach ($options_values as $option_value)
                    array_push($product_option_value_ids, $option_value->getProductOptionValueId());
            }
        }

        return [in_array(null, $product_option_value_ids, true), $product_option_value_ids];
    }

    private function checkIsOptionChanged($first_options_values)
    {
        $updated_option_ids = [];
        foreach ($first_options_values as $option_value) {
            array_push($updated_option_ids, $option_value->option_id);
        }
        return $this->containsOnlyNull($updated_option_ids);

    }

    private function containsOnlyNull($input)
    {
        return empty(array_filter($input, function ($a) {
            return $a !== null;
        }));
    }

    private function makeData()
    {
        $data = [];
        if (isset($this->categoryId)) $data['category_id'] = $this->categoryId;
        if (isset($this->name)) $data['name'] = $this->name;
        if (isset($this->description)) $data['description'] = $this->description;
        if (isset($this->warranty)) $data['warranty'] = $this->warranty;
        if (isset($this->warrantyUnit)) $data['warranty_unit'] = $this->warrantyUnit;
        if (isset($this->vatPercentage)) $data['vat_percentage'] = $this->vatPercentage;
        if (isset($this->unitId)) $data['unit_id'] = $this->unitId;
        return $data;
    }
}

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
use App\Services\Product\Logs\ProductUpdateLogCreateRequest;
use App\Services\Product\Update\NatureFactory;
use App\Services\Product\Update\Operations\NonVariant;
use App\Services\Product\Update\Operations\OptionsUpdated;
use App\Services\Product\Update\Operations\ValuesAdded;
use App\Services\Product\Update\Operations\ValuesUpdated;
use App\Services\Product\Update\Operations\ValuesDeleted;
use App\Services\Product\Update\Operations\VariantsAdd;
use App\Services\Product\Update\Operations\VariantsDiscard;
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
    /** @var mixed */
    private $deletedValues;
    private $natureFactory;
    /** @var ProductUpdateLogCreateRequest */
    private ProductUpdateLogCreateRequest $logCreateRequest;
    private $hasVariants;


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
     * @param SkuRepositoryInterface $skuRepositoryInterface
     * @param NatureFactory $natureFactory
     * @param ProductUpdateLogCreateRequest $logCreateRequest
     */
    public function __construct(ProductRepositoryInterface $productRepositoryInterface, DiscountCreator $discountCreator, ProductImageCreator $productImageCreator,
                                OptionRepositoryInterface $optionRepositoryInterface, ValueRepositoryInterface  $valueRepositoryInterface, ProductOptionRepositoryInterface $productOptionRepositoryInterface,
                                ProductOptionValueRepositoryInterface $productOptionValueRepositoryInterface, CombinationRepositoryInterface  $combinationRepositoryInterface,
                                ProductChannelRepositoryInterface $productChannelRepositoryInterface, SkuRepositoryInterface $skuRepositoryInterface,
                                NatureFactory $natureFactory, ProductUpdateLogCreateRequest $logCreateRequest)
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
        $this->natureFactory = $natureFactory;
        $this->logCreateRequest = $logCreateRequest;
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
        $oldProductDetails = clone $this->product;
        $this->productRepositoryInterface->update($this->product, $this->makeData());
        list($nature, $deleted_values) = $this->natureFactory->getNature($this->product, $this->productUpdateRequestObjects, $this->hasVariants);

        if($nature == UpdateNature::NON_VARIANT)
            app(NonVariant::class)->setProduct($this->product)->setUpdatedDataObjects($this->productUpdateRequestObjects)->apply();
        elseif($nature == UpdateNature::VARIANTS_DISCARD)
            app(VariantsDiscard::class)->setProduct($this->product)->setUpdatedDataObjects($this->productUpdateRequestObjects)->apply();
        elseif ($nature == UpdateNature::OPTIONS_UPDATED)
            app(OptionsUpdated::class)->setProduct($this->product)->setUpdatedDataObjects($this->productUpdateRequestObjects)->apply();
        elseif($nature == UpdateNature::VALUES_UPDATED)
            app(ValuesUpdated::class)->setNature($nature)->setProduct($this->product)->setDeletedValues($deleted_values)->setUpdatedDataObjects($this->productUpdateRequestObjects)->apply();
        elseif($nature == UpdateNature::VALUE_ADD)
            app(ValuesAdded::class)->setNature($nature)->setProduct($this->product)->setUpdatedDataObjects($this->productUpdateRequestObjects)->apply();
        else
            app(ValuesDeleted::class)->setNature($nature)->setProduct($this->product)->setDeletedValues($deleted_values)->setUpdatedDataObjects($this->productUpdateRequestObjects)->apply();
        $this->logCreateRequest->setOldProductDetails($oldProductDetails)->setUpdatedProductDetails($this->product)->create();
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

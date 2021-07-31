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
use App\Services\Discount\Creator as DiscountCreator;
use App\Services\Product\Logs\ProductUpdateLogCreateRequest;
use App\Services\Product\Update\Strategy\ProductUpdateStrategy;
use App\Services\Product\Update\Strategy\Updater as ProductUpdater;
use App\Services\Product\Update\StrategyFactory;
use App\Services\ProductImage\Creator as ProductImageCreator;
use App\Services\ProductImage\Updater as ProductImageUpdater;
use Illuminate\Support\Facades\DB;

class Updater
{
    protected Product $product;
    protected int $partnerId;
    protected int $categoryId;
    protected string $name;
    protected ?string $description;
    protected ?int $warranty;
    protected ?string $warrantyUnit;
    protected ?float $vatPercentage;
    protected ?int $unitId;
    /** @var ProductUpdateDetailsObjects[] */
    protected array $productUpdateRequestObjects;
    private bool $hasVariants;
    protected ?float $discountAmount;
    protected $discountEndDate;
    protected $images;
    protected $deletedImages;


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
     * @param ProductUpdateLogCreateRequest $logCreateRequest
     * @param ProductImageUpdater $productImageUpdater
     * @param StrategyFactory $strategyFactory
     * @param ProductUpdater $productUpdater
     */
    public function __construct(
        protected ProductRepositoryInterface $productRepositoryInterface,
        protected DiscountCreator $discountCreator,
        protected ProductImageCreator $productImageCreator,
        protected OptionRepositoryInterface $optionRepositoryInterface,
        protected ValueRepositoryInterface  $valueRepositoryInterface,
        protected ProductOptionRepositoryInterface $productOptionRepositoryInterface,
        protected ProductOptionValueRepositoryInterface $productOptionValueRepositoryInterface,
        protected CombinationRepositoryInterface  $combinationRepositoryInterface,
        protected ProductChannelRepositoryInterface $productChannelRepositoryInterface,
        protected SkuRepositoryInterface $skuRepositoryInterface,
        protected ProductUpdateLogCreateRequest $logCreateRequest,
        protected ProductImageUpdater $productImageUpdater,
        protected StrategyFactory $strategyFactory,
        protected ProductUpdater $productUpdater){}


    /**
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product): Updater
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @param int $categoryId
     * @return $this
     */
    public function setCategoryId(int $categoryId): Updater
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): Updater
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): Updater
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param int|null $warranty
     * @return $this
     */
    public function setWarranty(?int $warranty): Updater
    {
        $this->warranty = $warranty;
        return $this;
    }


    /**
     * @param string|null $warrantyUnit
     * @return $this
     */
    public function setWarrantyUnit(?string $warrantyUnit): Updater
    {
        $this->warrantyUnit = $warrantyUnit;
        return $this;
    }

    /**
     * @param float|null $vatPercentage
     * @return $this
     */
    public function setVatPercentage(?float $vatPercentage): Updater
    {
        $this->vatPercentage = $vatPercentage;
        return $this;
    }

    /**
     * @param int|null $unitId
     * @return $this
     */
    public function setUnitId(?int $unitId): Updater
    {
        $this->unitId = $unitId;
        return $this;
    }

    /**
     * @param float|null $discount_amount
     * @return $this
     */
    public function setDiscount(?float $discount_amount): Updater
    {
        $this->discountAmount = $discount_amount;
        return $this;
    }

    /**
     * @param $discount_end_date
     * @return $this
     */
    public function setDiscountEndDate($discount_end_date): Updater
    {
        $this->discountEndDate = $discount_end_date;
        return $this;
    }

    /**
     * @param $images
     * @return $this
     */
    public function setImages($images): Updater
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @param $deletedImages
     * @return $this
     */
    public function setDeletedImages($deletedImages): Updater
    {
        $this->deletedImages = $deletedImages;
        return $this;
    }

    /**
     * @param ProductUpdateDetailsObjects[] $productUpdateRequestObjects
     * @return $this
     */
    public function setProductUpdateRequestObjects(array $productUpdateRequestObjects): Updater
    {
        $this->productUpdateRequestObjects = $productUpdateRequestObjects;
        return $this;
    }

    /**
     * @param bool $hasVariants
     * @return $this
     */
    public function setHasVariant(bool $hasVariants): Updater
    {
        $this->hasVariants = $hasVariants;
        return $this;
    }

    public function update()
    {
        try {
            DB::beginTransaction();
            $oldProductDetails = clone $this->product;
            $this->productImageUpdater->updateImageList($this->images, $this->deletedImages, $this->product);
            $this->productRepositoryInterface->update($this->product, $this->makeData());
            $strategy = $this->strategyFactory->getStrategy($this->product, $this->productUpdateRequestObjects, $this->hasVariants);
            $this->productUpdater->setStrategy($strategy)
                ->setProduct($this->product)
                ->setUpdatedDataObjects($this->productUpdateRequestObjects)
                ->setDeletedValues($this->strategyFactory->getDeletedValues())
                ->update();
            $this->logCreateRequest->setOldProductDetails($oldProductDetails)->setUpdatedProductDetails($this->product)->create();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    private function makeData(): array
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

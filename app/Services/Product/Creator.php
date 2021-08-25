<?php namespace App\Services\Product;

use App\Exceptions\AuthorizationException;
use App\Interfaces\DiscountRepositoryInterface;
use App\Interfaces\PartnerRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Partner;
use App\Models\Sku;
use App\Services\AccessManager\AccessManager;
use App\Services\Channel\Channels;
use App\Services\Discount\Types;
use App\Services\Product\Logs\ProductUpdateLogCreateRequest;
use App\Services\ProductImage\Creator as ProductImageCreator;
use App\Services\Sku\CreateSkuDto;
use App\Services\Sku\Creator as SkuCreator;
use App\Services\SkuBatch\SkuBatchDto;
use App\Services\Warranty\WarrantyUnits;
use App\Services\Discount\Creator as DiscountCreator;
use App\Services\SkuBatch\Creator as SkuBatchCreator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class Creator
{
    protected ProductRepositoryInterface $productRepositoryInterface;
    protected DiscountRepositoryInterface $discountRepositoryInterface;
    protected $partnerId;
    protected $categoryId;
    protected $name;
    protected $description;
    protected $warranty;
    protected $warrantyUnit;
    protected $vatPercentage;
    protected $unitId;
    protected $discountAmount;
    protected $discountEndDate;
    protected $images;
    protected $wholesalePrice;
    protected $cost;
    protected $price;
    protected $stock;
    protected $channelIds;
    protected $accounting_info;

    /** @var ProductImageCreator */
    protected ProductImageCreator $productImageCreator;
    /** @var DiscountCreator */
    protected DiscountCreator $discountCreator;
    protected $productDetails;
    protected $productOptionRepositoryInterface;
    private $productOptionCreator;
    private $productOptionValueCreator;
    private $combinationCreator;
    private $productChannelCreator;
    private $productRequestObjects;
    private $hasVariants;
    private ?UploadedFile $app_thumb;
    private ?string $app_thumb_url = null;

    public function __construct(ProductRepositoryInterface $productRepositoryInterface,ProductOptionCreator $productOptionCreator,
                                ProductOptionValueCreator $productOptionValueCreator,CombinationCreator $combinationCreator,
                                DiscountCreator $discountCreator, ProductImageCreator $productImageCreator,
                                ProductChannelCreator $productChannelCreator, DiscountRepositoryInterface $discountRepositoryInterface, private SkuCreator $skuCreator,
                                protected SkuBatchCreator $skuBatchCreator, protected ProductUpdateLogCreateRequest $logCreateRequest,
                                protected AccessManager $accessManager, protected PartnerRepositoryInterface $partnerRepository
    )
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->productImageCreator = $productImageCreator;
        $this->discountCreator = $discountCreator;
        $this->productOptionCreator = $productOptionCreator;
        $this->productOptionValueCreator = $productOptionValueCreator;
        $this->combinationCreator = $combinationCreator;
        $this->productChannelCreator = $productChannelCreator;
        $this->discountRepositoryInterface = $discountRepositoryInterface;
    }


    /**
     * @param mixed $partnerId
     * @return Creator
     */
    public function setPartnerId($partnerId)
    {
        $this->partnerId = $partnerId;
        return $this;
    }

    /**
     * @param mixed $categoryId
     * @return Creator
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    /**
     * @param mixed $name
     * @return Creator
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $description
     * @return Creator
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param mixed $warranty
     * @return Creator
     */
    public function setWarranty($warranty)
    {
        $this->warranty = $warranty;
        return $this;
    }

    /**
     * @param mixed $warrantyUnit
     * @return Creator
     */
    public function setWarrantyUnit($warrantyUnit)
    {
        $this->warrantyUnit = $warrantyUnit;
        return $this;
    }

    /**
     * @param mixed $vatPercentage
     * @return Creator
     */
    public function setVatPercentage($vatPercentage)
    {
        $this->vatPercentage = $vatPercentage;
        return $this;
    }

    /**
     * @param mixed $unitId
     * @return Creator
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

    /**
     * @param mixed $images
     * @return Creator
     */
    public function setImages($images)
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @param mixed $app_thumb
     * @return Creator
     */
    public function setAppThumb($app_thumb)
    {
        $this->app_thumb = $app_thumb;
        return $this;
    }

    /**
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @return Creator
     */
    public function setProductRepositoryInterface(ProductRepositoryInterface $productRepositoryInterface): Creator
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
        return $this;
    }

    /**
     * @param mixed $wholesalePrice
     * @return Creator
     */
    public function setWholesalePrice($wholesalePrice)
    {
        $this->wholesalePrice = $wholesalePrice;
        return $this;
    }

    /**
     * @param mixed $cost
     * @return Creator
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
        return $this;
    }

    /**
     * @param mixed $price
     * @return Creator
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @param mixed $stock
     * @return Creator
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
        return $this;
    }

    /**
     * @param mixed $channelId
     * @return Creator
     */
    public function setChannelId($channelId)
    {
        $this->channelId = $channelId;
        return $this;
    }

    /**
     * @param $productRequestObjects
     * @return $this
     */
    public function setProductRequestObjects($productRequestObjects)
    {
      $this->productRequestObjects = $productRequestObjects;
      return $this;
    }

    public function setHasVariant($hasVariants)
    {
        $this->hasVariants = $hasVariants;
        return $this;
    }

    /**
     * @param mixed $accounting_info
     */
    public function setAccountingInfo($accounting_info)
    {
        $this->accounting_info = json_decode($accounting_info,true);
        return $this;
    }
    /**
     * @return mixed
     */
    public function create()
    {
        try {
            DB::beginTransaction();
            if ($this->app_thumb) $this->saveAppThumbImage();
            $product =  $this->productRepositoryInterface->create($this->makeData());
            $this->hasVariants ? $this->createVariantsSKUAndSKUChannels($product) : $this->createSKUAndSKUChannels($product);
            if ($this->discountAmount)
                $this->createProductDiscount($product);
            if ($this->images)
                $this->createImageGallery($product);
//            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * @param $product
     * @throws UnknownProperties
     */
    private function createVariantsSKUAndSKUChannels($product)
    {
        $all_channels = [];
        foreach($this->productRequestObjects as $productDetailObject)
        {
            /** @var  $productDetailObject ProductDetailsObject */
            $combinations = $productDetailObject->getCombination();
            $product_option_value_ids = [];
            $values = [];
            foreach($combinations as $combination)
            {
                /** @var $combination CombinationDetailsObject */
                $option_name = $combination->getoption();
                $product_option = $this->createProductOptions($product->id, $option_name);
                $value_name = $combination->getValue();
                $value_details = $combination->getValueDetails();
                $product_option_value = $this->createProductOptionValues($product_option->id, $value_name, $value_details);
                array_push($product_option_value_ids,$product_option_value->id);
                array_push($values,$value_name);
            }
            $sku = $this->skuCreator->create(new CreateSkuDto([
                'name' => $product->name . '-' . implode("-", $values),
                'product_id' => $product->id,
                'weight' => $productDetailObject->getWeight(),
                'weight_unit' => $productDetailObject->getWeightUnit()
            ]));
             $channels = $this->createSkuChannels($sku,$productDetailObject->getChannelData());
             $this->createSkuBatch($sku,$productDetailObject);
             array_push($all_channels,$channels);
             $this->createCombination($sku->id,$product_option_value_ids);
        }
        $all_channels = array_merge(...$all_channels);
        $this->createProductChannel($product->id,$all_channels);
    }

    /**
     * @throws AuthorizationException
     */
    private function createProductChannel($product_id, $channels)
    {
        $product_channels = [];
        $channels = array_unique($channels);
        foreach ($channels as $channel) {
            if ($channel == Channels::WEBSTORE) $this->accessManager
                ->setPartnerId($this->partnerId)
                ->setFeature('pos.ecom.product_publish')
                ->setProductPublishedCount($this->partnerRepository->getPartnerPublishedProductsCount($this->partnerId))
                ->checkAccess();
            array_push($product_channels, [
                'channel_id' => $channel,
                'product_id' => $product_id
            ]);
        }
        return $this->productChannelCreator->setData($product_channels)->store();
    }

    /**
     * @param $sku_id
     * @param $product_option_value_ids
     * @return mixed
     */
    private function createCombination($sku_id, $product_option_value_ids)
    {
       $combinations = collect($product_option_value_ids)->map(function($product_option_value_id) use($sku_id){
            return [
               'product_option_value_id' => $product_option_value_id,
               'sku_id' => $sku_id
           ] ;
       });
       return  $this->combinationCreator->setData($combinations->toArray())->store();
    }

    /**
     * @param $sku
     * @param $channel_data
     * @return mixed
     */
    private function createSkuChannels($sku, $channel_data)
    {
        $channels  = [];
        foreach($channel_data as $channel)
        {
           $data = [
               'sku_id'             => $sku->id,
               'channel_id'         => $channel->getChannelId(),
               'price'              => $channel->getPrice() ?: 0,
               'wholesale_price'    => $channel->getWholeSalePrice() ?: null
           ];
           $skuChannelData = $sku->skuChannels()->create($data);
           $this->discountCreator->setProductSkusDiscountData($skuChannelData->id, $channel);
           array_push($channels,$channel->getChannelId());
        }
        return $channels;
    }


    /**
     * @param $product_id
     * @param $option_name
     * @return mixed
     */
    private function createProductOptions($product_id, $option_name)
    {
       return $this->productOptionCreator->setProductId($product_id)->setOptionName($option_name)->create();
    }

    /**
     * @param $product_option_id
     * @param $value_name
     * @param $value_details
     * @return mixed
     */
    private function createProductOptionValues($product_option_id, $value_name, $value_details)
    {
        return $this->productOptionValueCreator->setProductOptionId($product_option_id)->setValueName($value_name)->setValueDetails($value_details)->create();
    }

    /**
     * @param $product
     * @throws UnknownProperties
     */
    private function createSKUAndSKUChannels($product)
    {
        $stock = $this->productRequestObjects[0]->getStock();
        $weight = $this->productRequestObjects[0]->getWeight();
        $weight_unit = $this->productRequestObjects[0]->getWeightUnit();
        $sku = $this->skuCreator->create(new CreateSkuDto([
            "name" => $product->name ,
            "product_id" => $product->id,
            "weight" => $weight,
            "weight_unit" => $weight_unit
            ]
        ));
        $this->createSkuBatch($sku,$this->productRequestObjects[0]);
        $channels = $this->createSKUChannels($sku, $this->productRequestObjects[0]->getChannelData());
        $this->createProductChannel($product->id,$channels);
    }


    /**
     * @param $product
     */
    private function createProductDiscount($product)
    {
        $this->discountCreator->setDiscount($this->discountAmount)
            ->setDiscountEndDate($this->discountEndDate)
            ->setDiscountTypeId($product->id)
            ->setDiscountType(Types::PRODUCT)
            ->create();
    }

    /**
     * @param $product
     */
    private function createImageGallery($product)
    {
        $this->productImageCreator->setProductId($product->id)->setImages($this->images)->create();
    }

    private function saveAppThumbImage()
    {
        $this->app_thumb_url = $this->productImageCreator->setAppThumb($this->app_thumb)->setFilename($this->name)->createAppThumb();
    }

    /**
     * @return array
     */
    private function makeData(): array
    {
        $data = [
            'partner_id' => $this->partnerId,
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'description' => $this->description,
            'warranty' => $this->warranty ?: 0,
            'warranty_unit' => $this->warrantyUnit ?: WarrantyUnits::DAY,
            'vat_percentage' => $this->vatPercentage ?: 0,
            'unit_id' => $this->unitId
        ];
       if ($this->app_thumb_url)  $data = array_merge($data, ['app_thumb' => $this->app_thumb_url ?? null]);
       return $data;
    }

    private function createSkuBatch(Sku $sku, ProductDetailsObject $product_detail_object)
    {
        $this->skuBatchCreator->create(new SkuBatchDto([
            "sku_id" => $sku->id,
            "stock" => $product_detail_object->getStock() ?: 0,
            "cost" => $product_detail_object->getCost() ?: 0,
            "supplier_id" => $this->accounting_info['supplier_id'] ?? null,
            "from_account" => $this->accounting_info['from_account'] ?? null,
        ]));
    }


}

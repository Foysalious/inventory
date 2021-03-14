<?php namespace App\Services\Product;

use App\Interfaces\DiscountRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Services\Discount\Types;
use App\Services\ProductImage\Creator as ProductImageCreator;
use App\Services\Warranty\Units;
use App\Services\Discount\Creator as DiscountCreator;

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



    public function __construct(ProductRepositoryInterface $productRepositoryInterface,ProductOptionCreator $productOptionCreator,
                                ProductOptionValueCreator $productOptionValueCreator,CombinationCreator $combinationCreator,
                                DiscountCreator $discountCreator, ProductImageCreator $productImageCreator, ProductChannelCreator $productChannelCreator)
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->productImageCreator = $productImageCreator;
        $this->discountCreator = $discountCreator;
        $this->productOptionCreator = $productOptionCreator;
        $this->productOptionValueCreator = $productOptionValueCreator;
        $this->combinationCreator = $combinationCreator;
        $this->productChannelCreator = $productChannelCreator;
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
     * @param $productDetails
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
     * @return mixed
     */
    public function create()
    {
        $product =  $this->productRepositoryInterface->create($this->makeData());
        $this->hasVariants ? $this->createVariantsSKUAndSKUChannels($product) : $this->createSKUAndSKUChannels($product);

        if ($this->discountAmount)
            $this->createProductDiscount($product);
        if ($this->images)
            $this->createImageGallery($product);

        return $product;
    }

    /**
     * @param $product
     */
    private function createVariantsSKUAndSKUChannels($product)
    {
        foreach($this->productRequestObjects as $productDetailObject)
        {
            $combinations = $productDetailObject->getCombination();
            $product_option_value_ids = [];
            $values = [];
            foreach($combinations as $combination)
            {
                $option_name = $combination->getoption();
                $product_option = $this->createProductOptions($product->id, $option_name);
                $value_name = $combination->getValue();
                $product_option_value = $this->createProductOptionValues($product_option->id, $value_name);
                array_push($product_option_value_ids,$product_option_value->id);
                array_push($values,$value_name);
            }

             $sku = $this->createSku($product,$values,$product->id,$productDetailObject->getStock());
             $this->createSkuChannels($sku,$productDetailObject->getChannelData());
             $this->createCombination($sku->id,$product_option_value_ids);
             $this->createProductChannel($productDetailObject->getChannelData(),$product->id);
        }
    }

    /**
     * @param $product
     * @param $values
     * @param $product_id
     * @param $stock
     * @return mixed
     */
    private function createSku($product, $values, $product_id, $stock)
    {
        $sku_data = [
            'name' => implode("-",$values) ,
            'product_id' => $product_id,
            'stock' => $stock,
        ];
        return $product->skus()->create($sku_data);
    }

    /**
     * @param $channels
     * @param $product_id
     * @return mixed
     */
    private function createProductChannel($channels, $product_id)
    {
        $product_channels =   collect($channels)->map(function($channel) use($product_id) {
            return [
                'product_id' => $product_id,
                'channel_id' =>   $channel->getChannelId(),
            ];
        });

        return $this->productChannelCreator->setData($product_channels->toArray())->store();
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
        $data = [];
        foreach($channel_data as $channel)
        {
           array_push($data,[
               'sku_id' => $sku->id,
               'channel_id' => $channel->getChannelId(),
               'cost' =>  $channel->getCost() ?: 0,
               'price' => $channel->getPrice() ?: 0,
               'wholesale_price' => $channel->getWholeSalePrice() ?: null
           ]);
        }
       return  $sku->skuChannels()->insert($data);

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
     * @return mixed
     */
    private function createProductOptionValues($product_option_id, $value_name)
    {
        return $this->productOptionValueCreator->setProductOptionId($product_option_id)->setValueName($value_name)->create();
    }

    /**
     * @param $product
     */
    private function createSKUAndSKUChannels($product)
    {
        $stock = $this->productRequestObjects[0]->getProductDetails()->getStock() > 0 ?: 0;
        $sku = $product->skus()->create(["product_id" => $product->id, "stock" => $stock ?: 0]);
        $this->createSKUChannels($sku,$this->productRequestObjects[0]->getChannelData());
        $this->createProductChannel($this->productRequestObjects[0]->getChannelData(),$product->id);
    }


    /**
     * @param $product
     */
    private function createProductDiscount($product)
    {
        $this->discountCreator->setDiscount($this->discountAmount)->setDiscountEndDate($this->discountEndDate)->setDiscountTypeId($product->id)->setDiscountType(Types::PRODUCT)->create();
    }

    /**
     * @param $product
     */
    private function createImageGallery($product)
    {
        $this->productImageCreator->setProductId($product->id)->setImages($this->images)->create();
    }

    /**
     * @return array
     */
    private function makeData()
    {
        return [
            'partner_id' => $this->partnerId,
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'description' => $this->description,
            'warranty' => $this->warranty ?: 0,
            'warranty_unit' => $this->warrantyUnit ?: Units::DAY,
            'vat_percentage' => $this->vatPercentage ?: 0,
            'unit_id' => $this->unitId,
        ];
    }
}

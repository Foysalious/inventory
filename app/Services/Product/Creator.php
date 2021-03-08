<?php namespace App\Services\Product;


use App\Interfaces\CombinationRepositoryInterface;
use App\Interfaces\DiscountRepositoryInterface;
use App\Interfaces\OptionRepositoryInterface;
use App\Interfaces\ProductChannelRepositoryInterface;
use App\Interfaces\ProductOptionRepositoryInterface;
use App\Interfaces\ProductOptionValueRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\ValueRepositoryInterface;
use App\Services\Discount\Types;
use App\Services\ProductImage\Creator as ProductImageCreator;
use App\Services\Warranty\Units;
use Carbon\Carbon;
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
    protected $optionRepositoryInterface;
    protected $valueRepositoryInterface;
    protected $productOptionRepositoryInterface;
    protected $productOptionValueRepositoryInterface;
    protected $combinationRepositoryInterface;
    protected $productChannelRepositoryInterface;


    /**
     * Creator constructor.
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
                                ProductOptionValueRepositoryInterface $productOptionValueRepositoryInterface, CombinationRepositoryInterface  $combinationRepositoryInterface, ProductChannelRepositoryInterface $productChannelRepositoryInterface)
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

    public function setProductDetails($productDetails)
    {
      $this->productDetails = json_decode($productDetails);
      return $this;
    }

    public function create()
    {

        $product =  $this->productRepositoryInterface->create($this->makeData());
        is_null($this->productDetails[0]->combination) ?  $this->createSKUAndSKUChannels($product) : $this->createVariantsSKUAndSKUChannels($product);

        if ($this->discountAmount)
            $this->createProductDiscount($product);
        if ($this->images)
            $this->createImageGallery($product);

        return $product;
    }

    private function createVariantsSKUAndSKUChannels($product)
    {
        foreach($this->productDetails as $productDetail)
        {
            $combinations = $productDetail->combination;
            $product_option_value_ids = [];
            $values = [];
            foreach($combinations as $combination)
            {
                $option_name = $this->optionRepositoryInterface->find($combination->option_id)->name;
                $product_option = $this->createProductOptions($product->id,$option_name);
                $value_name = $this->valueRepositoryInterface->find($combination->value_id)->name;
                $product_option_value = $this->createProductOptionValues($product_option->id, $value_name);
                array_push($product_option_value_ids,$product_option_value->id);
                array_push($values,$value_name);
            }

            $sku_data = [
                'name' => implode("-",$values) ,
                'product_id' => $product->id,
                'stock' => $productDetail->stock,
            ];
            $sku = $product->skus()->create($sku_data);

            $sku_channels = $this->createSkuChannelsData($sku,$productDetail->channel_data);
            $sku->skuChannels()->insert($sku_channels);
            $combinations = $this->createCombinationData($sku->id,$product_option_value_ids);
            $this->combinationRepositoryInterface->insert($combinations->toArray());
            $product_channels = $this->makeProductChannelData($productDetail->channel_data,$product->id);
            $this->productChannelRepositoryInterface->insert($product_channels->toArray());

        }
    }

    private function makeProductChannelData($channels, $product_id)
    {
       return  collect($channels)->map(function($channel) use($product_id) {
            return [
                'product_id' => $product_id,
                'channel_id' =>   $channel->channel_id,
            ];
        });
    }

    private function createCombinationData($sku_id,$product_option_value_ids)
    {
       return  collect($product_option_value_ids)->map(function($product_option_value_id) use($sku_id){
            return [
               'product_option_value_id' => $product_option_value_id,
                'sku_id' => $sku_id
           ] ;

        });
    }

    private function createSkuChannelsData($sku,$channel_data)
    {
        $data = [];
        foreach($channel_data as $channel)
        {
           array_push($data,[
               'sku_id' => $sku->id,
               'channel_id' => $channel->channel_id,
               'cost' =>  $channel->cost ?: 0,
               'price' => $channel->price ?: 0,
               'wholesale_price' => $channel->wholesale_price ?: null
           ]);
        }
        return $data;
    }

    private function createProductOptionValues($product_option_id, $value_name)
    {
        $data = [
            'product_option_id' => $product_option_id,
            'name' => $value_name
        ];
        return $this->productOptionValueRepositoryInterface->firstOrCreate($data);
    }

    private function createProductOptions($product_id,$option_name)
    {
        $data = [
          'product_id' => $product_id,
          'name' => $option_name
        ];
       return $this->productOptionRepositoryInterface->firstOrCreate($data);
    }

    private function createSKUAndSKUChannels($product)
    {
        $stock = $this->productDetails[0]->stock > 0 ?: 0;
        $sku = $product->skus()->create(["product_id" => $product->id, "stock" => $stock ?: 0]);
        $skuChannelsData = $this->makeSKUChannelsData($sku);
        $sku->skuChannels()->insert($skuChannelsData);

        $product_channels = $this->makeProductChannelData($this->productDetails[0]->channel_data,$product->id);
        $this->productChannelRepositoryInterface->insert($product_channels->toArray());

    }


    private function makeSKUChannelsData($sku)
    {
        $data = [];
        $channels = $this->productDetails[0]->channel_data;
        foreach($channels as $channel)
        {
            $temp['sku_id'] = $sku->id;
            $temp['channel_id'] = $channel->channel_id;
            $temp['cost'] = $channel->cost? : 0;
            $temp['price'] = $channel->price ? : 0;
            $temp['wholesale_price'] = $channel->wholesale_price ? : null;
            array_push($data,$temp);
        }

        return $data;
    }

    private function createProductDiscount($product)
    {
        $this->discountCreator->setDiscount($this->discountAmount)->setDiscountEndDate($this->discountEndDate)->setDiscountTypeId($product->id)->setDiscountType(Types::PRODUCT)->create();
    }

    private function createImageGallery($product)
    {
        $this->productImageCreator->setProductId($product->id)->setImages($this->images)->create();
    }

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

<?php namespace App\Services\Product\Update\Operations;

use App\Interfaces\CombinationRepositoryInterface;
use App\Interfaces\ProductOptionValueRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\SkuChannelRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Models\Product;
use App\Services\Product\CombinationCreator;
use App\Services\Product\ProductChannelCreator;
use App\Services\Product\ProductOptionCreator;
use App\Services\Product\ProductOptionValueCreator;
use App\Services\Product\UpdateNature;


class ValuesAddedDeleted
{

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepositoryInterface;
    /**
     * @var CombinationRepositoryInterface
     */
    private CombinationRepositoryInterface $combinationRepository;
    /**
     * @var ProductOptionValueRepositoryInterface
     */
    private ProductOptionValueRepositoryInterface $productOptionValueRepository;
    /**
     * @var SkuRepositoryInterface
     */
    private SkuRepositoryInterface $skuRepository;
    /**
     * @var SkuChannelRepositoryInterface
     */
    private SkuChannelRepositoryInterface $skuChannelRepository;
    private $productOptionCreator;
    private $productOptionValueCreator;
    private $combinationCreator;
    private $productChannelCreator;
    private $nature;

    public function __construct(ProductRepositoryInterface $productRepositoryInterface,
                                ProductOptionValueRepositoryInterface $productOptionValueRepository,
                                CombinationRepositoryInterface $combinationRepository,
                                SkuRepositoryInterface $skuRepository, SkuChannelRepositoryInterface $skuChannelRepository,
                                ProductOptionCreator $productOptionCreator, ProductOptionValueCreator $productOptionValueCreator,
                                CombinationCreator $combinationCreator, ProductChannelCreator $productChannelCreator)
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->productOptionValueRepository = $productOptionValueRepository;
        $this->combinationRepository = $combinationRepository;
        $this->skuRepository = $skuRepository;
        $this->skuChannelRepository = $skuChannelRepository;
        $this->productOptionCreator = $productOptionCreator;
        $this->productOptionValueCreator = $productOptionValueCreator;
        $this->combinationCreator = $combinationCreator;
        $this->productChannelCreator = $productChannelCreator;

    }

    /**
     * @var Product $product
     */
    private $product;

    private $updateDataObejects;

    private $deletedValues;


    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdateDataObjects()
    {
        return $this->updateDataObejects;
    }

    /**
     * @param mixed $updateDataObejects
     */
    public function setUpdatedDataObjects($updateDataObejects)
    {
        $this->updateDataObejects = $updateDataObejects;
        return $this;
    }

    public function setNature($nature)
    {
      $this->nature =   $nature;
      return $this;
    }

    public function apply()
    {
        if($this->nature == UpdateNature::VALUE_ADD_DELETE || $this->nature == UpdateNature::VALUE_DELETE)
        $this->deleteDiscardedCombinations();
        if($this->nature == UpdateNature::VALUE_ADD_DELETE || $this->nature == UpdateNature::VALUE_ADD)
        $this->operationsForValueAdd();
    }

    private function operationsForValueAdd()
    {
        foreach($this->updateDataObejects as $productDetailObject)
        {
            $combinations = $productDetailObject->getCombination();
            $sku_channels = $productDetailObject->getChannelData();
            list($is_old,$related_skus) = $this->checkAndApplyOperationIfOldCombination($combinations,$productDetailObject);
            if($is_old)
            {
                $this->updateSkuChannels($sku_channels,$related_skus);
                continue;
            }

            $product_option_value_ids = [];
            $values = [];
            foreach($combinations as $combination)
            {
                $option_name = $combination->getOptionName();
                $product_option = $this->createProductOptions($this->product->id, $option_name);
                $value_name = $combination->getOptionValueName();
                $product_option_value = $this->createProductOptionValues($product_option->id, $value_name);
                array_push($product_option_value_ids, $product_option_value->id);
                array_push($values, $value_name);

            }
            $sku = $this->createSku($this->product, $values, $this->product->id, $productDetailObject->getStock());
            $this->createSkuChannels($sku, $sku_channels);
            $this->createCombination($sku->id, $product_option_value_ids);
            // $this->createProductChannel($productDetailObject->getChannelData(), $this->product->id);
        }
    }
    private function createSku($product, $values, $product_id, $stock)
    {
        $sku_data = [
            'name' => implode("-", $values),
            'product_id' => $product_id,
            'stock' => $stock,
        ];
        return $product->skus()->create($sku_data);
    }

    private function createSkuChannels($sku, $channel_data)
    {
        $data = [];
        foreach ($channel_data as $channel) {
            array_push($data, [
                'sku_id' => $sku->id,
                'channel_id' => $channel->getChannelId(),
                'cost' => $channel->getCost() ?: 0,
                'price' => $channel->getPrice() ?: 0,
                'wholesale_price' => $channel->getWholeSalePrice() ?: null
            ]);
        }
        return $sku->skuChannels()->insert($data);
    }

    private function createCombination($sku_id, $product_option_value_ids)
    {
        $combinations = collect($product_option_value_ids)->map(function ($product_option_value_id) use ($sku_id) {
            return [
                'product_option_value_id' => $product_option_value_id,
                'sku_id' => $sku_id
            ];
        });
        return $this->combinationCreator->setData($combinations->toArray())->store();
    }

    private function createProductChannel($channels, $product_id)
    {
        $product_channels = collect($channels)->map(function ($channel) use ($product_id) {
            return [
                'product_id' => $product_id,
                'channel_id' => $channel->getChannelId(),
            ];
        });

        return $this->productChannelCreator->setData($product_channels->toArray())->store();
    }

    private function createProductOptions($product_id, $option_name)
    {
        return $this->productOptionCreator->setProductId($product_id)->setOptionName($option_name)->create();
    }

    private function createProductOptionValues($product_option_id, $value_name)
    {
        return $this->productOptionValueCreator->setProductOptionId($product_option_id)->setValueName($value_name)->create();
    }

    private function updateSkuChannels($sku_channels,$related_skus)
    {
        list($is_deleted,$deleted_sku_Channels) = $this->checkAndApplyOperationIfSkuChannelsDeleted($sku_channels,$related_skus);
        if($is_deleted)
            $this->skuChannelRepository->whereIn('id',$deleted_sku_Channels)->delete();
    }

    private function checkAndApplyOperationIfSkuChannelsDeleted($sku_channels,$related_skus)
    {
        $created_sku_channels_ids = $this->skuChannelRepository->whereIn('sku_id',$related_skus)->pluck('id')->toArray();
        $updated_sku_channels_ids = [];
        foreach ($sku_channels as $sku_channel) {
            $sku_channel_id = $sku_channel->getSkuChannelId();
            array_push($updated_sku_channels_ids, $sku_channel_id);
            if($sku_channel_id)//old sku_channel
            {
                $this->skuChannelRepository->where('id',$sku_channel_id)->update([
                    'cost' => $sku_channel->getCost(),
                    'price' => $sku_channel->getPrice(),
                    'wholesale_price' => $sku_channel->getWholesalePrice()
                ]);
            } else { //new sku_channel
                $this->product->skus()->each(function ($sku) use ($sku_channel) {
                    $this->skuChannelRepository->create([
                        'sku_id' => $sku->id,
                        'channel_id' => $sku_channel->getChannelId(),
                        'cost' => $sku_channel->getCost(),
                        'price' => $sku_channel->getPrice(),
                        'wholesale_price' => $sku_channel->getWholesalePrice()
                    ]);
                });
                $product_channel_data = [
                    'product_id' => $this->product->id,
                    'channel_id' => $sku_channel->getChannelId(),
                ];
                $this->productChannelCreator->setData($product_channel_data)->store();
            }
        }
        $filtered_updated_sku_channels_ids = array_filter($updated_sku_channels_ids, function ($a) {
            return $a !== null;
        });
        $deleted_sku_channel_ids = null;
        $is_deleted = $created_sku_channels_ids != $filtered_updated_sku_channels_ids;
        if($is_deleted) //checkPoint for notDeleted
            $deleted_sku_channel_ids   = array_diff($created_sku_channels_ids,$filtered_updated_sku_channels_ids);
        return [$is_deleted , $deleted_sku_channel_ids ];
    }

    private function checkAndApplyOperationIfOldCombination($combination,$sku)
    {
        $is_old =  !is_null($combination[0]->getOptionValueId());
        $old_skus = null;

        if($is_old)
        {
            $old_product_option_value_ids = [];
            foreach($combination as $option_values)
            {
                array_push($old_product_option_value_ids,$option_values->getOptionValueId());
            }

            $stock = $sku->getStock();
            $old_skus = $this->combinationRepository->whereIn('product_option_value_id',$old_product_option_value_ids)->pluck('sku_id')->toArray();
            $this->skuRepository->whereIn('id',$old_skus)->update(['stock' => $stock ]);

        }
        return [$is_old,$old_skus];
    }

    private function deleteDiscardedCombinations()
    {
      $this->productOptionValueRepository->whereIn('id',$this->deletedValues)->delete();
      $skus_to_delete = $this->combinationRepository->whereIn('product_option_value_id',$this->deletedValues)->pluck('sku_id');
      $this->skuRepository->whereIn('id',$skus_to_delete)->delete();
      $this->skuChannelRepository->whereIn('sku_id',$skus_to_delete)->delete();
      $this->combinationRepository->whereIn('product_option_value_id',$this->deletedValues)->delete();
    }

    /**
     * @return mixed
     */
    public function getDeletedValues()
    {
        return $this->deletedValues;
    }

    /**
     * @param mixed $deletedValues
     */
    public function setDeletedValues($deletedValues)
    {
        $this->deletedValues = $deletedValues;
        return $this;
    }


}

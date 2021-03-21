<?php namespace App\Services\Product\Update\Operations;


use App\Interfaces\CombinationRepositoryInterface;
use App\Interfaces\ProductOptionValueRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\SkuChannelRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Models\Product;


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

    public function __construct(ProductRepositoryInterface $productRepositoryInterface,
                                ProductOptionValueRepositoryInterface $productOptionValueRepository,
                                CombinationRepositoryInterface $combinationRepository,
                                SkuRepositoryInterface $skuRepository, SkuChannelRepositoryInterface $skuChannelRepository
                               )
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->productOptionValueRepository = $productOptionValueRepository;
        $this->combinationRepository = $combinationRepository;
        $this->skuRepository = $skuRepository;
        $this->skuChannelRepository = $skuChannelRepository;

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
    public function getUpdateDataObejects()
    {
        return $this->updateDataObejects;
    }

    /**
     * @param mixed $updateDataObejects
     */
    public function setUpdateDataObejects($updateDataObejects)
    {
        $this->updateDataObejects = $updateDataObejects;
        return $this;
    }

    public function apply()
    {
        $this->deleteDiscardedCombinations();
        foreach($this->updateDataObejects as $sku)
        {
            $combination = $sku->getCombination();
            $sku_channels = $sku->getSkuChannels();
            if($this->checkAndApplyOperationIfOldCombination($combination))
            {
                $this->updateSkuChannels($sku_channels);
                continue;
            }


            foreach($combination as $option_value)
            {
                //operations for new skus
            }
        }
    }

    private function updateSkuChannels($sku_channels)
    {
        list($is_deleted,$deleted_sku_Channels) = $this->checkAndApplyOperationIfSkuChannelsDeleted($sku_channels);
        if($is_deleted)
        {
            //delete discraded sku_channels
        }

    }

    private function checkAndApplyOperationIfSkuChannelsDeleted($sku_channels)
    {
        $created_sku_channels_ids = [];
        $is_deleted = false;
        $this->product->skus()->each(function ($sku) use ($created_sku_channels_ids) {
            array_push($created_sku_channels_ids, $sku->skuChannels()->pluck('id')->toArray());
        });
        $updated_sku_channels_ids = [];

        foreach ($sku_channels as $sku_channel) {
            array_push($updated_sku_channels_ids, $sku_channel->sku_channel_id);
            if($sku_channel_id = $sku_channel->sku_channel_id)
            {
                $this->skuChannelRepository->where('id',$sku_channel_id)->update([
                    'channel_id' => $sku_channel->channel_id,
                    'cost' => $sku_channel->cost,
                    'price' => $sku_channel->price,
                    'wholesale_price' => $sku_channel->wholesale_price
                ]);
            }else{

                $this->product->skus()->each(function ($sku) use ($sku_channel) {
                    $this->skuChannelRepository->create([
                        'sku_id' => $sku->id,
                        'channel_id' => $sku_channel->channel_id,
                        'cost' => $sku_channel->cost,
                        'price' => $sku_channel->price,
                        'wholesale_price' => $sku_channel->wholesale_price

                    ]);
                });

            }
        }

        $filtered_updated_sku_channels_ids = array_filter($updated_sku_channels_ids, function ($a) {
            return $a !== null;
        });
        $deleted_sku_channel_ids = null;
        $is_deleted = $created_sku_channels_ids != $filtered_updated_sku_channels_ids;
        if($is_deleted)
            $deleted_sku_channel_ids   = array_diff($created_sku_channels_ids,$filtered_updated_sku_channels_ids);
        return [$is_deleted , $deleted_sku_channel_ids ];



    }

    private function checkAndApplyOperationIfOldCombination($combination)
    {
        $is_old =  !is_null($combination[0]['product_option_value_id']);
        if($is_old)
        {
            $old_product_option_value_ids = [];
            foreach($combination as $options_values)
            {
                foreach($options_values as $option_value)
                {
                    array_push($old_product_option_value_ids,$option_value->getProductOptionValueId());
                }
            }

            $stock = $combination->getStock();
            $old_skus = $this->combinationRepository->whereIn('product_opotion_value_id',$old_product_option_value_ids)->pluck('sku_id')->toArray();
            $this->skuRepository->whereIn('id',$old_skus)->update(['stock' => $stock ]);

        }
        return $is_old;
    }

    private function deleteDiscardedCombinations()
    {
      $this->productOptionValueRepository->whereIn('id',$this->deletedValues)->delete();
      $skus_to_delete = $this->combinationRepository->whereIn('product_option_value_id',$this->deletedValues)->pluck('sku_id');
      $this->skuRepository->whereIn('id',$skus_to_delete)->delete();
      $this->skuChannelRepository->whereIn('sku_id',$skus_to_delete)->delete();

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

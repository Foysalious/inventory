<?php namespace App\Services\Product\Update\Strategy;


use App\Interfaces\DiscountRepositoryInterface;
use App\Interfaces\SkuChannelRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Models\Sku;
use App\Services\Discount\Creator as DiscountCreator;
use App\Services\Discount\Types;
use App\Services\Product\ProductChannelCreator;
use App\Services\Product\ProductStockBatchUpdater;
use App\Services\Sku\CreateSkuDto;
use App\Services\Sku\Creator as SkuCreator;
use App\Services\SkuBatch\SkuBatchDto;
use App\Services\SkuBatch\Updater as SkuStockUpdater;
use Carbon\Carbon;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

abstract class ProductUpdate
{
    protected $channels = [];
    protected $product;
    protected $updateDataObjects;
    protected $deletedValues;

    public function __construct(
        protected SkuRepositoryInterface $skuRepository,
        protected SkuCreator $skuCreator,
        protected SkuStockUpdater $skuStockUpdater,
        protected SkuChannelRepositoryInterface $skuChannelRepository,
        protected ProductStockBatchUpdater $productStockBatchUpdater,
        protected DiscountRepositoryInterface $discountRepository,
        protected DiscountCreator $discountCreator,
        protected ProductChannelCreator $productChannelCreator){}

    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setUpdatedDataObjects($updateDataObjects)
    {
        $this->updateDataObjects = $updateDataObjects;
        return $this;
    }

    public function setDeletedValues($deletedValues)
    {
        $this->deletedValues = $deletedValues;
        return $this;
    }

    public function getDeletedValues()
    {
        return $this->deletedValues;
    }

    /**
     * @throws UnknownProperties
     */
    protected function createSku($product, $updateDataObject): Sku
    {
        $combinations = $updateDataObject->getCombination();
        $values = [];
        foreach($combinations as $combination) {
            $value_name = $combination->getOptionValueName();
            array_push($values, $value_name);
        }
        $weight = $updateDataObject->getWeight();
        $weight_unit = $updateDataObject->getWeightUnit();
        return $this->skuCreator->create(new CreateSkuDto([
                'name' => $product->name . '-' . implode("-", $values),
                "product_id" => $product->id,
                "weight" => $weight,
                "weight_unit" => $weight_unit
            ]
        ));
    }

    /**
     * @throws UnknownProperties
     */
    protected function updateStock($sku, $updateDataObjects)
    {
        $sku_dto = new SkuBatchDto(
            [
                "sku_id" => $sku->id,
                "cost" => $updateDataObjects->getCost(),
                "stock" => $this->updateDataObjects[0]->getStock(),
            ]
        );
        $this->skuStockUpdater->setSkuBatchDto($sku_dto)->update();
    }

    protected function deleteBatchStock()
    {
        $this->productStockBatchUpdater->deleteBatchStock($this->product);
    }

    protected function createSkuChannels($sku, $channel_data)
    {
        $channels = [];
        foreach ($channel_data as $channel) {
            $data = [];
            array_push($data, [
                'sku_id' => $sku->id,
                'channel_id' => $channel->getChannelId() ?? $channel->channel_id,
                'price' => $channel->getPrice() ?? $channel->price,
                'wholesale_price' => $channel->getWholeSalePrice() ?? $channel->wholesale_price
            ]);
            array_push($channels, $channel->getChannelId());
            $skuChannelData = $this->skuChannelRepository->create($data[0]);
            $this->discountCreator->setProductSkusDiscountData($skuChannelData->id, $channel);
        }
        return $channels;
    }

    protected function updateSkuChannels($sku_channels, $related_skus)
    {
        list($is_deleted, $deleted_sku_Channels) = $this->checkAndApplyOperationIfSkuChannelsDeleted($sku_channels, $related_skus);
        if($is_deleted) {
            $this->skuChannelRepository->whereIn('id', $deleted_sku_Channels)->delete();
            $this->discountRepository->whereIn('type_id', $deleted_sku_Channels)->where('type', Types::SKU_CHANNEL)->delete();
        }
    }

    protected function deleteProductChannels()
    {
        return $this->product->productChannels()->delete();
    }

    protected function createProductChannel($product_id, $channels)
    {
        $product_channels = [];
        $channels = array_unique($channels);
        foreach ($channels as $channel) {
            array_push($product_channels, [
                'channel_id' => $channel,
                'product_id' => $product_id
            ]);
        }
        return $this->productChannelCreator->setData($product_channels)->store();
    }

    protected function checkAndApplyOperationIfSkuChannelsDeleted($sku_channels, $related_skus)
    {
        $created_sku_channels_ids = $this->skuChannelRepository->where('sku_id',$related_skus)->pluck('id')->toArray();
        $updated_sku_channels_ids = [];
        foreach ($sku_channels as $sku_channel) {
            $sku_channel_id = $sku_channel->getSkuChannelId();
            array_push($this->channels, $sku_channel->getChannelId());
            array_push($updated_sku_channels_ids, $sku_channel_id);
            if($sku_channel_id) //old sku_channel
            {
                $this->skuChannelRepository->where('id',$sku_channel_id)->update([
                    'price' => $sku_channel->getPrice(),
                    'wholesale_price' => $sku_channel->getWholesalePrice()
                ]);
                $this->discountRepository->where('type_id', $sku_channel_id)->update([
                    'type' => Types::SKU_CHANNEL,
                    'details' => $sku_channel->getDiscountDetails(),
                    'amount' => $sku_channel->getDiscount(),
                    'is_amount_percentage' => $sku_channel->getIsPercentage(),
                    'end_date' => $sku_channel->getDiscountEndDate()
                ]);
            }
            else { //new sku_channel
                $this->skuChannelRepository->create([
                    'sku_id' => $related_skus,
                    'channel_id' => $sku_channel->getChannelId(),
                    'price' => $sku_channel->getPrice(),
                    'wholesale_price' => $sku_channel->getWholesalePrice()
                ]);
                $this->discountRepository->create([
                    'type_id' => $related_skus,
                    'type' => Types::SKU_CHANNEL,
                    'details' => $sku_channel->getDiscountDetails(),
                    'amount' => $sku_channel->getDiscount(),
                    'is_amount_percentage' => $sku_channel->getIsPercentage(),
                    'start_date' => Carbon::now(),
                    'end_date' => $sku_channel->getDiscountEndDate()
                ]);
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


}

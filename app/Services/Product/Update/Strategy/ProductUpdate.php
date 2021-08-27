<?php namespace App\Services\Product\Update\Strategy;


use App\Exceptions\AuthorizationException;
use App\Interfaces\DiscountRepositoryInterface;
use App\Interfaces\SkuChannelRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Models\Product;
use App\Models\Sku;
use App\Services\Channel\Channels;
use App\Services\Discount\Creator as DiscountCreator;
use App\Services\Discount\Types;
use App\Services\Product\ChannelUpdateDetailsObjects;
use App\Services\Product\CheckProductPublishAccess;
use App\Services\Product\ProductChannelCreator;
use App\Services\Product\ProductStockBatchUpdater;
use App\Services\Product\ProductUpdateDetailsObjects;
use App\Services\Sku\CreateSkuDto;
use App\Services\Sku\Creator as SkuCreator;
use App\Services\SkuBatch\SkuBatchDto;
use App\Services\SkuBatch\Updater as SkuStockUpdater;
use Carbon\Carbon;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

abstract class ProductUpdate implements ProductUpdateStrategy
{
    protected Product $product;
    protected bool $hasVariant;
    /** @var ProductUpdateDetailsObjects[] */
    protected array $updateDataObjects;
    protected ?array $deletedValues;
    protected ?array $channels = [];

    public function __construct(
        protected SkuRepositoryInterface $skuRepository,
        protected SkuCreator $skuCreator,
        protected SkuStockUpdater $skuStockUpdater,
        protected SkuChannelRepositoryInterface $skuChannelRepository,
        protected ProductStockBatchUpdater $productStockBatchUpdater,
        protected DiscountRepositoryInterface $discountRepository,
        protected DiscountCreator $discountCreator,
        protected ProductChannelCreator $productChannelCreator,
        protected CheckProductPublishAccess $productPublishAccess)
    {
    }

    public function setProduct(Product $product): ProductUpdate
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @param bool $hasVariant
     * @return ProductUpdate
     */
    public function setHasVariant(bool $hasVariant): ProductUpdate
    {
        $this->hasVariant = $hasVariant;
        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setUpdatedDataObjects($updateDataObjects): ProductUpdate
    {
        $this->updateDataObjects = $updateDataObjects;
        return $this;
    }

    public function setDeletedValues($deletedValues): ProductUpdate
    {
        $this->deletedValues = $deletedValues;
        return $this;
    }

    public function getDeletedValues(): ?array
    {
        return $this->deletedValues;
    }


    /**
     * @param Product $product
     * @param ProductUpdateDetailsObjects $updateDataObject
     * @return Sku
     * @throws UnknownProperties
     */
    protected function createSku(Product $product, ProductUpdateDetailsObjects $updateDataObject): Sku
    {
        $combinations = $updateDataObject->getCombination();
        $values = [];
        foreach ($combinations as $combination) {
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
     * @param $sku
     * @param ProductUpdateDetailsObjects[] $updateDataObjects
     * @throws UnknownProperties
     */
    protected function updateStock($sku, array $updateDataObjects): void
    {
        $sku_dto = new SkuBatchDto(
            [
                "sku_id" => $sku->id,
                "cost" => $updateDataObjects[0]->getCost(),
                "stock" => $this->updateDataObjects[0]->getStock(),
            ]
        );
        $this->skuStockUpdater->setSkuBatchDto($sku_dto)->update();
    }

    protected function deleteBatchStock(): void
    {
        $this->productStockBatchUpdater->deleteBatchStock($this->product);
    }

    /**
     * @param Sku $sku
     * @param ChannelUpdateDetailsObjects[] $channel_data
     * @return array
     */
    protected function createSkuChannels(Sku $sku, array $channel_data): array
    {
        $channels = [];
        foreach ($channel_data as $channel) {
            $data = [];
            array_push($data, [
                'sku_id' => $sku->id,
                'channel_id' => $channel->getChannelId(), //?? $channel->channel_id
                'price' => $channel->getPrice(), //?? $channel->price
                'wholesale_price' => $channel->getWholeSalePrice() //?? $channel->wholesale_price
            ]);
            array_push($channels, $channel->getChannelId());
            $skuChannelData = $this->skuChannelRepository->create($data[0]);
            $this->discountCreator->setProductSkusDiscountData($skuChannelData->id, $channel);
        }
        return $channels;
    }

    /**
     * @param ChannelUpdateDetailsObjects[] $sku_channels
     * @param int $related_skus
     */
    protected function updateSkuChannels(array $sku_channels, int $related_skus): void
    {
        /** @var bool $is_deleted */
        /** @var array $deleted_sku_Channels */
        list($is_deleted, $deleted_sku_Channels) = $this->checkAndApplyOperationIfSkuChannelsDeleted($sku_channels, $related_skus);
        if ($is_deleted) {
            $this->skuChannelRepository->whereIn('id', $deleted_sku_Channels)->delete();
            $this->discountRepository->whereIn('type_id', $deleted_sku_Channels)->where('type', Types::SKU_CHANNEL)->delete();
        }
    }

    protected function deleteProductChannels()
    {
        return $this->product->productChannels()->delete();
    }

    /**
     * @throws AuthorizationException
     */
    protected function createProductChannel(int $product_id, array $channels)
    {
        $product_channels = [];
        $channels = array_unique($channels);
        if (in_array(Channels::WEBSTORE, $channels)) $this->productPublishAccess->check($this->product->partner_id);
        foreach ($channels as $channel) {
            array_push($product_channels, [
                'channel_id' => $channel,
                'product_id' => $product_id
            ]);
        }
        return $this->productChannelCreator->setData($product_channels)->store();
    }

    /**
     * @param ChannelUpdateDetailsObjects[] $sku_channels
     * @param int $related_skus
     * @return array
     */
    protected function checkAndApplyOperationIfSkuChannelsDeleted(array $sku_channels, int $related_skus): array
    {
        /** @var array $created_sku_channels_ids */
        $created_sku_channels_ids = $this->skuChannelRepository->where('sku_id', $related_skus)->pluck('id')->toArray();
        $updated_sku_channels_ids = [];
        foreach ($sku_channels as $sku_channel) {
            $sku_channel_id = $sku_channel->getSkuChannelId();
            array_push($this->channels, $sku_channel->getChannelId());
            array_push($updated_sku_channels_ids, $sku_channel_id);
            if ($sku_channel_id) //old sku_channel
            {
                $this->skuChannelRepository->where('id', $sku_channel_id)->update([
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
            } else { //new sku_channel
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
        /** @var ?array $deleted_sku_channel_ids */
        $deleted_sku_channel_ids = null;
        $is_deleted = $created_sku_channels_ids != $filtered_updated_sku_channels_ids;
        if ($is_deleted)
            $deleted_sku_channel_ids = array_diff($created_sku_channels_ids, $filtered_updated_sku_channels_ids);
        return [$is_deleted, $deleted_sku_channel_ids];
    }

    abstract public function update();
}

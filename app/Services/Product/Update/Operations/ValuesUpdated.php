<?php namespace App\Services\Product\Update\Operations;

use App\Interfaces\CombinationRepositoryInterface;
use App\Interfaces\DiscountRepositoryInterface;
use App\Interfaces\ProductOptionValueRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\SkuChannelRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Models\Product;
use App\Repositories\SkuBatchRepository;
use App\Services\Discount\Creator;
use App\Services\Discount\Types;
use App\Services\Product\CombinationCreator;
use App\Services\Product\ProductChannelCreator;
use App\Services\Product\ProductOptionCreator;
use App\Services\Product\ProductOptionValueCreator;
use App\Services\Product\ProductStockBatchUpdater;
use App\Services\Product\UpdateNature;
use App\Services\Sku\CreateSkuDto;
use App\Services\Sku\Creator as SkuCreator;
use App\Services\SkuBatch\SkuBatchDto;
use Carbon\Carbon;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use App\Services\SkuBatch\Updater as SkuStockUpdater;


class ValuesUpdated
{
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
    protected SkuRepositoryInterface $skuRepository;
    /**
     * @var SkuChannelRepositoryInterface
     */
    private SkuChannelRepositoryInterface $skuChannelRepository;
    private $productOptionCreator;
    private $productOptionValueCreator;
    private $combinationCreator;
    private $productChannelCreator;
    private $channels = [];
    /**
     * @var Product $product
     */
    protected $product;
    /** @var DiscountRepositoryInterface $discountRepository */
    protected DiscountRepositoryInterface $discountRepository;

    protected $updateDataObejects;

    protected $deletedValues, $nature;

    public function __construct(ProductOptionValueRepositoryInterface $productOptionValueRepository,
                                CombinationRepositoryInterface $combinationRepository,
                                DiscountRepositoryInterface $discountRepository,
                                SkuRepositoryInterface $skuRepository, SkuChannelRepositoryInterface $skuChannelRepository,
                                ProductOptionCreator $productOptionCreator, ProductOptionValueCreator $productOptionValueCreator,
                                CombinationCreator $combinationCreator, ProductChannelCreator $productChannelCreator, private SkuCreator $skuCreator,
                                protected SkuStockUpdater $skuStockUpdater,
                                protected SkuBatchRepository $skuBatchRepository,
                                protected ProductStockBatchUpdater $productStockBatchUpdater
    )
    {
        $this->productOptionValueRepository = $productOptionValueRepository;
        $this->combinationRepository = $combinationRepository;
        $this->skuRepository = $skuRepository;
        $this->skuChannelRepository = $skuChannelRepository;
        $this->productOptionCreator = $productOptionCreator;
        $this->productOptionValueCreator = $productOptionValueCreator;
        $this->combinationCreator = $combinationCreator;
        $this->productChannelCreator = $productChannelCreator;
        $this->discountRepository = $discountRepository;
    }

    /**
     * @param mixed $nature
     * @return ValuesUpdated
     */
    public function setNature($nature)
    {
        $this->nature = $nature;
        return $this;
    }

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

    public function apply()
    {
        $this->deleteDiscardedCombinations();
        $this->operationsForValueAdd();
        $this->resolveProductChannel();
    }

    protected function resolveProductChannel()
    {
        $product_channel = [];
        $this->product->productChannels()->delete();
        $product_id = $this->product->id;
        $channels = array_unique($this->channels);
        foreach($channels as $channel)
        {
            array_push($product_channel,[
               'product_id' =>  $product_id,
                'channel_id' => $channel
            ]);
        }
       return $this->productChannelCreator->setData($product_channel)->store();

    }

    protected function operationsForOldValues()
    {
        foreach ($this->updateDataObejects as $productDetailObject) {
            $combinations = $productDetailObject->getCombination();
            $sku_channels = $productDetailObject->getChannelData();
            $related_skus = $this->checkAndApplyOperationForOldCombination($combinations, $productDetailObject);
            $this->updateSkuChannels($sku_channels, $related_skus);
        }
    }

    /**
     * @throws UnknownProperties
     */
    protected function operationsForValueAdd()
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
            $sku = $this->skuCreator->create(new CreateSkuDto([
                'name' => implode("-", $values),
                'product_id' => $this->product->id,
                'stock' => $productDetailObject->getStock(),
                'weight' => $productDetailObject->getWeight(),
                'weight_unit' => $productDetailObject->getWeightUnit(),
            ]));
            $this->createSkuChannels($sku, $sku_channels);
            $this->createCombination($sku->id, $product_option_value_ids);
            $this->productStockBatchUpdater->createBatchStock($sku, $productDetailObject);
        }
    }

    private function createSkuChannels($sku, $channel_data)
    {
        foreach ($channel_data as $channel) {
            $data = [];
            array_push($data, [
                'sku_id' => $sku->id,
                'channel_id' => $channel->getChannelId(),
                'cost' => $channel->getCost() ?: 0,
                'price' => $channel->getPrice() ?: 0,
                'wholesale_price' => $channel->getWholeSalePrice() ?: null
            ]);
            array_push($this->channels, $channel->getChannelId());
            $skuChannelData = $this->skuChannelRepository->create($data[0]);
            /** @var $discountCreator Creator */
            $discountCreator = app(Creator::class);
            $discountCreator->setDiscountType(Types::SKU_CHANNEL)->setProductSkusDiscountData($skuChannelData->id, $channel);
        }
        return true;
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

    protected function updateSkuChannels($sku_channels,$related_skus)
    {
        list($is_deleted,$deleted_sku_Channels) = $this->checkAndApplyOperationIfSkuChannelsDeleted($sku_channels,$related_skus);
        if($is_deleted) {
            $this->skuChannelRepository->whereIn('id',$deleted_sku_Channels)->delete();
            $this->discountRepository->whereIn('type_id', $deleted_sku_Channels)->where('type', Types::SKU_CHANNEL)->delete();
        }
    }

    private function checkAndApplyOperationIfSkuChannelsDeleted($sku_channels,$related_skus)
    {
        $created_sku_channels_ids = $this->skuChannelRepository->where('sku_id',$related_skus)->pluck('id')->toArray();
        $updated_sku_channels_ids = [];
        foreach ($sku_channels as $sku_channel) {
            $sku_channel_id = $sku_channel->getSkuChannelId();
            array_push($this->channels,$sku_channel->getChannelId());
            array_push($updated_sku_channels_ids, $sku_channel_id);
            if($sku_channel_id)//old sku_channel
            {
                $this->skuChannelRepository->where('id',$sku_channel_id)->update([
                    'cost' => $sku_channel->getCost(),
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
                        'cost' => $sku_channel->getCost(),
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

    private function checkAndApplyOperationForOldCombination($combination,$sku)
    {
        $old_product_option_value_ids = [];
        foreach($combination as $option_values)
        {
            array_push($old_product_option_value_ids,$option_values->getOptionValueId());
        }

        $stock = $sku->getStock();
        $old_sku = $this->combinationRepository->whereIn('product_option_value_id',$old_product_option_value_ids)->pluck('sku_id')->first();
        $this->skuRepository->where('id',$old_sku)->update(['stock' => $stock ]);
        $this->productStockBatchUpdater->updateBatchStock($old_sku, $stock);
        return $old_sku;
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
            $old_skus = $this->combinationRepository->whereIn('product_option_value_id',$old_product_option_value_ids)->pluck('sku_id')->first();
            $this->skuRepository->where('id',$old_skus)->update(['stock' => $stock ]);
        }
        return [$is_old,$old_skus];
    }

    protected function deleteDiscardedCombinations()
    {
        $this->productOptionValueRepository->whereIn('id', $this->getDeletedValues())->delete();
        $skus_to_delete = $this->combinationRepository->whereIn('product_option_value_id', $this->deletedValues)->pluck('sku_id');
        $this->deleteSkusStockBatch($skus_to_delete);
        $skus_channels_to_delete = $this->skuChannelRepository->whereIn('sku_id', $skus_to_delete)->pluck('id');
        $this->skuRepository->whereIn('id', $skus_to_delete)->delete();
        $this->skuChannelRepository->whereIn('sku_id', $skus_to_delete)->delete();
        $this->combinationRepository->whereIn('product_option_value_id', $this->deletedValues)->delete();
        $this->deleteSkuChannelDiscount($skus_channels_to_delete);
    }

    protected function deleteSkuChannelDiscount($skus_channels_to_delete)
    {
        $this->discountRepository->whereIn('type_id', $skus_channels_to_delete)->where('type', Types::SKU_CHANNEL)->delete();
    }

    protected function updateStock($sku, $updateDataObjects)
    {
        $sku_dto = new SkuBatchDto(
            [
                "sku_id" => $sku->id,
                "cost" => $updateDataObjects[0]->getChannelData()[0]->getCost(),
                "stock" => $this->updateDataObejects[0]->getStock(),
            ]
        );
        $this->skuStockUpdater->setSkuBatchDto($sku_dto)->update();
    }

    protected function deleteSkusStockBatch($sku_ids)
    {
        $this->skuBatchRepository->whereIn('sku_id', $sku_ids)->delete();
    }

}

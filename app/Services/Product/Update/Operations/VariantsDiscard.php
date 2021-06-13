<?php namespace App\Services\Product\Update\Operations;


use App\Services\Discount\Creator;
use App\Services\Discount\Types;

class VariantsDiscard extends OptionsUpdated
{
    public function apply()
    {
        $this->deleteProductOptions();
        $this->deleteSkuAndCombination();
        $this->deleteProductChannels();
        $this->createSkuAndSkuChannels();
    }

    public function createSkuAndSkuChannels()
    {
        $stock = $this->updateDataObejects[0]->getStock();
        $sku = $this->product->skus()->create(["product_id" => $this->product->id, "stock" => $stock ?: 0]);
        $channels = $this->createSKUChannels($sku, $this->updateDataObejects[0]->getChannelData());
        $this->createProductChannel($this->product->id, $channels);
    }

    private function createSkuChannels($sku, $channel_data)
    {
        $channels = [];
        foreach ($channel_data as $channel) {
            $data = [];
            array_push($data, [
                'sku_id' => $sku->id,
                'channel_id' => $channel->getChannelId(),
                'cost' => $channel->getCost() ?: 0,
                'price' => $channel->getPrice() ?: 0,
                'wholesale_price' => $channel->getWholeSalePrice() ?: null
            ]);
            array_push($channels, $channel->getChannelId());
            $skuChannelData = $this->skuChannelRepository->create($data[0]);
            /** @var $discountCreator Creator */
            $discountCreator = app(Creator::class);
            $discountCreator->setDiscountType(Types::SKU_CHANNEL)->setProductSkusDiscountData($skuChannelData->id, $channel);
        }

        return $channels;
    }

    private function createProductChannel($product_id, $channels)
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


}

<?php namespace App\Events\Accounting;

use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductStockUpdated
{
    use Dispatchable, SerializesModels;

    protected Product $product;
    protected ProductUpdateRequest $productUpdateRequest;
    protected ?array $oldStockData;

    /**
     * ProductStockUpdated constructor.
     * @param Product $product
     * @param ProductUpdateRequest $request
     */
    public function __construct(Product $product, ProductUpdateRequest $request, $old_stock_data)
    {
        $this->product = $product;
        $this->productUpdateRequest = $request;
        $this->oldStockData = $old_stock_data;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return ProductUpdateRequest
     */
    public function getRequest(): ProductUpdateRequest
    {
        return $this->productUpdateRequest;
    }

    /**
     * @return array|null
     */
    public function getOldStockData(): ?array
    {
        return $this->oldStockData;
    }
}

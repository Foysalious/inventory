<?php namespace App\Events\Accounting;

use App\Http\Requests\ProductRequest;
use App\Http\Requests\SkuStockAddRequest;
use App\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductStockAdded
{
    use Dispatchable, SerializesModels;

    protected Product $product;
    protected ProductRequest | SkuStockAddRequest $requestObject;

    /**
     * ProductStockAdded constructor.
     * @param Product $product
     * @param ProductRequest|SkuStockAddRequest $request
     */
    public function __construct(Product $product, ProductRequest | SkuStockAddRequest $request)
    {
        $this->product = $product;
        $this->requestObject = $request;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return ProductRequest|SkuStockAddRequest
     */
    public function getRequestObject(): ProductRequest|SkuStockAddRequest
    {
        return $this->requestObject;
    }






}

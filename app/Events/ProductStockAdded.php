<?php

namespace App\Events;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductStockAdded
{
    use Dispatchable, SerializesModels;

    protected Product $product;
    protected ProductRequest $productRequest;

    /**
     * ProductStockAdded constructor.
     * @param Product $product
     * @param ProductRequest $request
     */
    public function __construct(Product $product, ProductRequest $request)
    {
        $this->product = $product;
        $this->productRequest = $request;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return ProductRequest
     */
    public function getRequest(): ProductRequest
    {
        return $this->productRequest;
    }




}

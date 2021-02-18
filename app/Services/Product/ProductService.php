<?php namespace App\Services\Product;


use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository;
use App\Traits\ResponseAPI;

class ProductService
{
    use ResponseAPI;

    /** @var ProductRepository */
    protected ProductRepository $productRepository;
    /** @var Creator */
    protected Creator $creator;

    public function __construct(ProductRepository $productRepository, Creator $creator)
    {
        $this->productRepository = $productRepository;
        $this->creator = $creator;
    }

    public function getDetails($product)
    {
        $resource = $this->productRepository->find($product);
        $product = new ProductResource($resource);
        return $this->success('Successful', $product, 200);
    }

    public function create($partnerId, ProductRequest $request)
    {
        $product = $this->creator->setPartnerId($partnerId)
            ->setCategoryId($request->id)
            ->setName($request->name)
            ->setDescription($request->description)
            ->setShowImage($request->show_image)
            ->setWarranty($request->warranty)
            ->setWarrantyUnit($request->warranty_unit)
            ->setVatPercentage($request->vat_percentage)
            ->setUnitId($request->unit_id)
            ->create();

        return $this->success("Successful", $product,201);
    }
}

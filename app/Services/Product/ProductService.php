<?php namespace App\Services\Product;


use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Interfaces\ProductRepositoryInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\JsonResponse;

class ProductService
{
    use ResponseAPI;

    /** @var ProductRepositoryInterface */
    protected ProductRepositoryInterface $productRepositoryInterface;
    /** @var Creator */
    protected Creator $creator;
    /** @var Updater */
    protected Updater $updater;

    public function __construct(ProductRepositoryInterface $productRepositoryInterface, Creator $creator, Updater $updater)
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->creator = $creator;
        $this->updater = $updater;
    }


    /**
     * @param $partner
     * @return JsonResponse
     */
    public function getProductList($partner)
    {
        $products = $this->productRepositoryInterface->where('partner_id',$partner)->get();
        $products = ProductResource::collection($products);
        return $this->success('Successful', $products, 200);
    }

    public function getDetails($product)
    {
        $resource = $this->productRepositoryInterface->find($product);
        $product = new ProductResource($resource);
        return $this->success('Successful', $product, 200);
    }

    public function create($partnerId, ProductRequest $request)
    {
        $product = $this->creator->setPartnerId($partnerId)
            ->setCategoryId($request->id)
            ->setName($request->name)
            ->setDescription($request->description)
            ->setWarranty($request->warranty)
            ->setWarrantyUnit($request->warranty_unit)
            ->setVatPercentage($request->vat_percentage)
            ->setUnitId($request->unit_id)
            ->create();

        return $this->success("Successful", $product,201);
    }

    public function update($productId, ProductUpdateRequest $request)
    {
        $product = $this->productRepositoryInterface->find($productId);
        $this->updater->setProduct($product)
            ->setCategoryId($request->category_id)
            ->setName($request->name)
            ->setDescription($request->description)
            ->setWarranty($request->warranty)
            ->setWarrantyUnit($request->warranty_unit)
            ->setVatPercentage($request->vat_percentage)
            ->setUnitId($request->unit_id)
            ->update();
        return $this->success("Successful", $product,200);
    }
}

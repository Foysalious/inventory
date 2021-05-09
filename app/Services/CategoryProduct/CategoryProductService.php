<?php namespace App\Services\CategoryProduct;


use App\Exceptions\ProductNotFoundException;
use App\Http\Resources\CategoryProductResource;
use App\Services\BaseService;
use App\Sheba\Product\ProductListWithCategory;
use Illuminate\Http\Request;

class CategoryProductService extends BaseService
{
    private ProductListWithCategory $productList;

    public function __construct(ProductListWithCategory $productListWithCategory)
    {
        $this->productList = $productListWithCategory;
    }


    public function getProducts($partner_id, Request $request)
    {
        list($offset, $limit) = calculatePagination($request);
        $this->productList->setPartnerId($partner_id);
        $this->productList->setOffset($offset);
        $this->productList->setLimit($limit);
        if ($request->has('master_category_ids')) $this->productList->setMasterCatgoryIds($request->master_category_ids);
        if ($request->has('category_ids')) $this->productList->setCategoryIds($request->category_ids);
        if ($request->has('updated_after')) $this->productList->setUpdatedAfter($request->updated_after);
        if ($request->has('is_published_for_webstore')) $this->productList->setWebstorePublicationStatus($request->is_published_for_webstore);
        $products = $this->productList->get();
        $request->request->add($products);
        if ($request->products->isEmpty())
            throw new ProductNotFoundException('স্টকে কোন পণ্য নেই! প্রয়োজনীয় তথ্য দিয়ে স্টকে পণ্য যোগ করুন।');
        $items = collect([]);
        $resource = new CategoryProductResource($items);
        return $this->success("Successful", ['category_products' => $resource]);
    }

}

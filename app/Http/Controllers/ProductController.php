<?php namespace App\Http\Controllers;

use App\Exceptions\ProductNotFoundException;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Services\Product\Constants\Log\FieldType;
use App\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /** @var ProductService */
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     *
     * * @OA\Get(
     *      path="/api/v1/partners/{partner}/products",
     *      operationId="getProducts",
     *      tags={"Partners Products API"},
     *      summary="Get Products List for POS by Partner",
     *      description="",
     *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="category_ids", description="category ids", required=false, in="query", @OA\Schema(type="array", @OA\Items(type="integer")) ),
     *      @OA\Parameter(name="sub_category_ids", description="sub category ids", required=false, in="query", @OA\Schema(type="array", @OA\Items(type="integer")) ),
     *      @OA\Parameter(name="updated_after", description="products updated after date", required=false, in="query", @OA\Schema(type="string") ),
     *      @OA\Parameter(name="offset", description="pagination offset", required=false, in="query", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="limit", description="pagination limit", required=false, in="query", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="is_published_for_webstore", description="publication filter for webstore", required=false, in="query", @OA\Schema(type="integer", enum={0,1}) ),
     *      @OA\Response(response=200, description="Successful operation",
     *          @OA\JsonContent(
     *          type="object",
     *          example={
     *               "message": "Successful",
     *               "data": {
     *                   "total_items": 2,
     *                   "total_buying_price": 1800,
     *                   "items_with_buying_price": 2,
     *                   "products": {{
     *                       "id": 1000328,
     *                       "sub_category_id": 10053,
     *                       "name": "sdf",
     *                       "vat_percentage": 5,
     *                       "unit": null,
     *                       "stock": 20,
     *                       "app_thumb": "https://s3.ap-south-1.amazonaws.com/cdn-shebadev/images/pos/services/thumbs/1608693744_jacket.jpeg",
     *                       "variations": {
     *                       {
     *                       "combination": {
     *                       {
     *                       "option_id": 744,
     *                       "option_name": "size",
     *                       "option_value_id": 1478,
     *                       "option_value_name": "l"
     *                       },
     *                       {
     *                       "option_id": 745,
     *                       "option_name": "color",
     *                       "option_value_id": 1479,
     *                       "option_value_name": "green"
     *                       }
     *                       },
     *                       "stock": 10,
     *                       "channel_data": {
     *                       {
     *                       "sku_channel_id": 1438,
     *                       "channel_id": 1,
     *                       "purchase_price": 90,
     *                       "original_price": 105,
     *                       "discounted_price": 95,
     *                       "discount": 10,
     *                       "is_discount_percentage": 0,
     *                       "wholesale_price": 105
     *                       },
     *                       {
     *                       "sku_channel_id": 1439,
     *                       "channel_id": 1,
     *                       "purchase_price": 90,
     *                       "original_price": 99.75,
     *                       "discounted_price": 99.75,
     *                       "discount": 0,
     *                       "is_discount_percentage": 0,
     *                       "wholesale_price": 105
     *                       }
     *                       }
     *                       },
     *                       {
     *                       "combination": {
     *                       {
     *                       "option_id": 744,
     *                       "option_name": "size",
     *                       "option_value_id": 1480,
     *                       "option_value_name": "s"
     *                       },
     *                       {
     *                       "option_id": 745,
     *                       "option_name": "color",
     *                       "option_value_id": 1481,
     *                       "option_value_name": "Black"
     *                       }
     *                       },
     *                       "stock": 10,
     *                       "channel_data": {
     *                       {
     *                       "sku_channel_id": 1440,
     *                       "channel_id": 2,
     *                       "purchase_price": 90,
     *                       "original_price": 105,
     *                       "discounted_price": 105,
     *                       "discount": 0,
     *                       "is_discount_percentage": 0,
     *                       "wholesale_price": 105
     *                       },
     *                       {
     *                       "sku_channel_id": 1441,
     *                       "channel_id": 1,
     *                       "purchase_price": 90,
     *                       "original_price": 99.75,
     *                       "discounted_price": 99.75,
     *                       "discount": 0,
     *                       "is_discount_percentage": 0,
     *                       "wholesale_price": 105
     *                       }
     *                       }
     *                       }
     *                       },
     *                       "created_at": "2021-05-16T17:57:32.000000Z"
     *                       }
     *                  },
     *                  "deleted_products": {},
     *              }
     *           },
     *       ),
     *      ),
     *      @OA\Response(response=404, description="message: স্টকে কোন পণ্য নেই! প্রয়োজনীয় তথ্য দিয়ে স্টকে পণ্য যোগ করুন।"),
     *      @OA\Response(response=403, description="Forbidden")
     *     )
     * @param $partner
     * @param Request $request
     * @return JsonResponse
     *
     * @throws ProductNotFoundException
     */
    public function index($partner, Request $request): JsonResponse
    {
        return $this->productService->getProducts($partner, $request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $partner
     * @param ProductRequest $request
     * @return JsonResponse
     */

    /**
     *
     * * @OA\POST (
     *      path="/api/v1/partners/{partner}/products",
     *      operationId="postProducts",
     *      tags={"Partners Products API"},
     *      summary="POST Product with SKU Data",
     *      description="Post a product with Sku data and discount",
     *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          @OA\MediaType(mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="name", type="String"),
     *                  @OA\Property(property="category_id", type="Integer"),
     *                  @OA\Property(property="description", type="Text"),
     *                  @OA\Property(property="warranty", type="Integer"),
     *                  @OA\Property(property="warranty_unit", type="String"),
     *                  @OA\Property(property="vat_percentage", type="Integer"),
     *                  @OA\Property(property="product_details", type="JSON"),
     *                  @OA\Property(property="image[0]", type="file"),
     *                  @OA\Property(property="sub_category_id", type="integer")
     *             )
     *         )
     *      ),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="message: Partner Not Found!"),
     *      @OA\Response(response=403, description="Forbidden")
     *     )
     **/

    public function store($partner, ProductRequest $request)
    {
        return $this->productService->create($partner, $request);
    }

    /**
     *  @OA\Get(
     *      path="/api/v1/partners/{partner_id}/products/{product_id}",
     *      operationId="getPrduct",
     *      tags={"Partners Products API"},
     *      summary="Get a particular product's detail of a partner",
     *      description="",
     *      @OA\Parameter(name="partner_id", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="product_id", description="product id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful", @OA\JsonContent(type="object",example={"message":"Successful","product":{"id":1000328,"category_id":10052,"sub_category_id":10053,"collection_id":null,"name":"Vegetable","description":null,"vat_percentage":5,"unit":{"id":4,"name_bn":"\u0995\u09c7\u099c\u09bf","name_en":"kg"},"stock":55,"rating":null,"count_rating":null,"app_thumb":"https:\/\/s3.ap-south-1.amazonaws.com\/cdn-shebadev\/images\/pos\/services\/thumbs\/default.jpg","warranty":0,"warranty_unit":"day","orginal_price":95,"variations":{{"sku_id":520,"combination":null,"stock":6,"channel_data":{{"sku_channel_id":1055,"channel_id":1,"purchase_price":90,"original_price":100,"discounted_price":100,"discount":0,"is_discount_percentage":0,"wholesale_price":99.75},{"sku_channel_id":1056,"channel_id":2,"purchase_price":90,"original_price":95,"discounted_price":95,"discount":0,"is_discount_percentage":0,"wholesale_price":99.75},{"sku_channel_id":1879,"channel_id":2,"purchase_price":90,"original_price":95,"discounted_price":95,"discount":0,"is_discount_percentage":0,"wholesale_price":105}}},{"sku_id":521,"combination":null,"stock":0,"channel_data":{{"sku_channel_id":1057,"channel_id":1,"purchase_price":90,"original_price":100,"discounted_price":100,"discount":0,"is_discount_percentage":0,"wholesale_price":99.75},{"sku_channel_id":1058,"channel_id":2,"purchase_price":90,"original_price":95,"discounted_price":95,"discount":0,"is_discount_percentage":0,"wholesale_price":99.75},{"sku_channel_id":1059,"channel_id":3,"purchase_price":90,"original_price":95,"discounted_price":95,"discount":0,"is_discount_percentage":0,"wholesale_price":99.75},{"sku_channel_id":1880,"channel_id":2,"purchase_price":90,"original_price":95,"discounted_price":95,"discount":0,"is_discount_percentage":0,"wholesale_price":105}}},{"sku_id":522,"combination":null,"stock":49,"channel_data":{{"sku_channel_id":1060,"channel_id":1,"purchase_price":90,"original_price":100,"discounted_price":100,"discount":0,"is_discount_percentage":0,"wholesale_price":94.5},{"sku_channel_id":1061,"channel_id":2,"purchase_price":90,"original_price":95,"discounted_price":95,"discount":0,"is_discount_percentage":0,"wholesale_price":99.75},{"sku_channel_id":1881,"channel_id":2,"purchase_price":90,"original_price":95,"discounted_price":95,"discount":0,"is_discount_percentage":0,"wholesale_price":105}}},{"sku_id":700,"combination":{{"option_id":744,"option_name":"size","option_value_id":1478,"option_value_name":"l"},{"option_id":745,"option_name":"color","option_value_id":1479,"option_value_name":"green"}},"stock":0,"channel_data":{{"sku_channel_id":1438,"channel_id":1,"purchase_price":90,"original_price":100,"discounted_price":90,"discount":10,"is_discount_percentage":0,"wholesale_price":105},{"sku_channel_id":1439,"channel_id":1,"purchase_price":90,"original_price":95,"discounted_price":95,"discount":0,"is_discount_percentage":0,"wholesale_price":105},{"sku_channel_id":1882,"channel_id":2,"purchase_price":90,"original_price":95,"discounted_price":95,"discount":0,"is_discount_percentage":0,"wholesale_price":105}}},{"sku_id":701,"combination":{{"option_id":744,"option_name":"size","option_value_id":1480,"option_value_name":"s"},{"option_id":745,"option_name":"color","option_value_id":1481,"option_value_name":"Black"}},"stock":0,"channel_data":{{"sku_channel_id":1440,"channel_id":2,"purchase_price":90,"original_price":100,"discounted_price":100,"discount":0,"is_discount_percentage":0,"wholesale_price":105},{"sku_channel_id":1441,"channel_id":1,"purchase_price":90,"original_price":95,"discounted_price":95,"discount":0,"is_discount_percentage":0,"wholesale_price":105},{"sku_channel_id":1883,"channel_id":2,"purchase_price":90,"original_price":95,"discounted_price":95,"discount":0,"is_discount_percentage":0,"wholesale_price":105}}},{"sku_id":816,"combination":{{"option_id":843,"option_name":"size","option_value_id":1616,"option_value_name":"l"},{"option_id":844,"option_name":"color","option_value_id":1617,"option_value_name":"green"}},"stock":0,"channel_data":{{"sku_channel_id":1607,"channel_id":1,"purchase_price":90,"original_price":100,"discounted_price":100,"discount":0,"is_discount_percentage":0,"wholesale_price":105},{"sku_channel_id":1608,"channel_id":2,"purchase_price":90,"original_price":95,"discounted_price":95,"discount":0,"is_discount_percentage":0,"wholesale_price":105},{"sku_channel_id":1884,"channel_id":2,"purchase_price":90,"original_price":95,"discounted_price":95,"discount":0,"is_discount_percentage":0,"wholesale_price":105}}}},"created_at":"2021-05-16T17:57:32.000000Z","image_gallery":{{"id":20,"image_link":"https:\/\/s3.ap-south-1.amazonaws.com\/cdn-shebadev\/partner\/pos-service-image-gallery\/1622096479_php8ayxed_product_image.jpeg"},{"id":41,"image_link":"https:\/\/s3.ap-south-1.amazonaws.com\/cdn-shebadev\/partner\/pos-service-image-gallery\/1622096479_php8ayxed_product_image.jpeg"},{"id":42,"image_link":"https:\/\/s3.ap-south-1.amazonaws.com\/cdn-shebadev\/partner\/pos-service-image-gallery\/1622096479_php8ayxed_product_image.jpeg"},{"id":46,"image_link":"https:\/\/s3.ap-south-1.amazonaws.com\/cdn-shebadev\/partner\/pos-service-image-gallery\/1622096479_php8ayxed_product_image.jpeg"}}}})),
     *      @OA\Response(response=404, description="message: Product is not found"),
     *      @OA\Response(response=403, description="This product does not belongs to this partner")
     *     )
     * @param $partner
     * @param $product
     * @return JsonResponse
     */
    public function show($partner, $product)
    {
        return $this->productService->getDetails($partner, $product);
    }

    /**
     * @param $partner
     * @param $product
     * @param ProductUpdateRequest $request
     * @return JsonResponse
     */

    /**
     *
     * * @OA\PUT (
     *      path="/api/v1/partners/{partner}/products/{product}",
     *      operationId="putProducts",
     *      tags={"Partners Products API"},
     *      summary="Update Product with SKU Data",
     *      description="Update a product with Sku data and discount",
     *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="product", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          @OA\MediaType(mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="name", type="String"),
     *                  @OA\Property(property="category_id", type="Integer"),
     *                  @OA\Property(property="description", type="Text"),
     *                  @OA\Property(property="warranty", type="Integer"),
     *                  @OA\Property(property="warranty_unit", type="String"),
     *                  @OA\Property(property="vat_percentage", type="Integer"),
     *                  @OA\Property(property="product_details", type="JSON", example={{"combination":null,"stock":10,"channel_data":{{"sku_channel_id":218,"channel_id":1,"price":100,"cost":90,"wholesale_price":100, "is_percentage":0,"discount":10,"discount_end_date":"2025-05-25 00:00:00", "discount_details": "It's good"},{"sku_channel_id":219,"channel_id":2,"price":95,"cost":90,"wholesale_price":100, "is_percentage":0,"discount":10,"discount_end_date":"2025-05-25 00:00:00", "discount_details": "It's good"}}}}),
     *                  @OA\Property(property="image[0]", type="file")
     *             )
     *         )
     *      ),
     *      @OA\Response(response=200, description="Successful operation"),
     *      @OA\Response(response=404, description="message: Partner Not Found!"),
     *      @OA\Response(response=403, description="Forbidden")
     *     )
     **/
    public function update($partner, $product, ProductUpdateRequest $request)
    {
        return $this->productService->update($product, $request, $partner);
    }

    public function destroy($partner, $product)
    {
        return $this->productService->delete($partner,$product);
    }

    /**
     * @param Request $request
     * @param $partner
     * @param Product $product
     * @return JsonResponse
     */

    public function getLogs(Request $request, $partner, $product)
    {
        return $this->productService->getLogs($request, $partner, $product);
    }
}

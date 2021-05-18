<?php namespace App\Services\Category;


use App\Exceptions\CategoryNotFoundException;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\CategoryWithSubCategory;
use App\Http\Resources\CategoryProductResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryWiseProductResource;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Interfaces\CategoryPartnerRepositoryInterface;
use App\Services\BaseService;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryService extends BaseService
{
    protected CategoryRepositoryInterface $categoryRepositoryInterface;

    /**
     * @var Updater
     */
    private Updater $updater;
    /**
     * @var Creator
     */
    private Creator $creator;

    private CategoryWithSubCategoryCreator $categoryWithSubCategoryCreator;

    private $partnerCategoryRepositoryInterface;

    /**
     * @var CategoryPartnerRepositoryInterface
     */
    private CategoryPartnerRepositoryInterface $categoryPartnerRepositoryInterface;
    private $productRepositoryInterface;
    private $authorization;

    public function __construct(CategoryRepository $categoryRepository,
                                CategoryRepositoryInterface $categoryRepositoryInterface,
                                CategoryPartnerRepositoryInterface $partnerCategoryRepositoryInterface,
                                Creator $creator, Updater $updater,
                                ProductRepositoryInterface $productRepositoryInterface,
                                CategoryWithSubCategoryCreator $categoryWithSubCategoryCreator,Authorization $authorization
    )

    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->partnerCategoryRepositoryInterface = $partnerCategoryRepositoryInterface;
        $this->creator = $creator;
        $this->updater = $updater;
        $this->categoryRepository = $categoryRepository;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->categoryWithSubCategoryCreator = $categoryWithSubCategoryCreator;
        $this->authorization = $authorization;

    }

    /**
     * @param $partner_id
     * @return JsonResponse
     * @throws CategoryNotFoundException
     */
    public function getCategoriesByPartner($partner_id)
    {
        $categories = $this->categoryRepositoryInterface->getCategoriesByPartner($partner_id);
        if ($categories->isEmpty())
            throw new CategoryNotFoundException('কোন ক্যাটাগরি যোগ করা হয়নি!');
        $resource = CategoryResource::collection($categories);
        $data['total_category'] = count($categories);
        $data['category'] = $resource;
        return $this->success("Successful", $data);
    }

    public function getCategoryByID($category_id,Request $request)
    {
        $products= $this->productRepositoryInterface->getProductsByCategoryId($category_id);
        $categories = $this->categoryRepositoryInterface->getProductsByCategoryId($category_id);
        $request->merge(['products' => $products]);
        $resource = CategoryWiseProductResource::collection($categories);
        if (count($resource) > 0) return $this->success("Successful", ['data' => $resource]);
        throw new NotFoundHttpException("No Category Found ");
    }


    /**
     * @param CategoryRequest $request
     * @param $partner_id
     * @return JsonResponse
     */
    public function create(CategoryRequest $request, $partner_id)
    {
        $category = $this->creator->setModifyBy($request->modifier)
            ->setPartner($partner_id)
            ->setName($request->name)
            ->setThumb($request->thumb ?? null)
            ->setParentId($request->parent_id ?? null)
            ->create();
        return $this->success("Successful", null,201);
    }

    /**
     * @param CategoryRequest $request
     * @param $partner
     * @param $category
     * @return JsonResponse
     */
    public function update(CategoryRequest $request, $partner_id, $category_id)
    {
        $category = $this->categoryRepositoryInterface->find($category_id);
        $category_partner = $category ? $category->categoryPartner()->where('partner_id', $partner_id)->where('category_id', $category_id)->get()->first() : null;
        if ( !$category || !$category_partner )
            throw new ModelNotFoundException();
        $this->authorization->setPartner($partner_id)->setCategory($category)->setType('update')->check();
        $this->updater->setModifyBy($request->modifier)->setCategory($category)->setCategoryId($category->id)->setName($request->name)->setThumb($request->thumb)->update();
        return $this->success("Successful", ['category' => $category],200);
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function delete($partner,$request)
    {
        $category_id = $request->category;
        $category = $this->categoryRepositoryInterface->where('id', $category_id)->with(['children' => function ($query) {
            $query->select('id', 'parent_id','is_published_for_sheba');
        }])->select('id')->first();
        if (!$category)
            return $this->error("Not Found", 404);
        $this->authorization->setPartner($partner)->setCategory($category)->setType('delete')->check();
        $children = $category->children->pluck('id')->toArray();
        $master_cat_with_children = array_merge($children, [$category->id]);
        $this->categoryRepositoryInterface->whereIn('id', $master_cat_with_children)->delete();

        $this->partnerCategoryRepositoryInterface->whereIn('category_id', $master_cat_with_children)->delete();
        $this->productRepositoryInterface->whereIn('category_id', $children)->delete();

        return $this->success("Successful", null, 200, false);
    }

    public function createCategoryWithSubCategory(CategoryWithSubCategory $request, $partner_id)
    {
        $this->categoryWithSubCategoryCreator->setModifyBy($request->modifier)
            ->setPartner($partner_id)
            ->setName($request->category_name)
            ->setThumb($request->category_thumb ?? null)
            ->setParentId($request->parent_id ?? null)
            ->setSubCategory($request->sub_category)
            ->create();
        return $this->success("Successful", null,201);
    }


}

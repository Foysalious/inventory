<?php namespace App\Services\Category;


use App\Exceptions\CategoryNotFoundException;
use App\Http\Requests\CategoryRequest;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\PartnerCategoryRepositoryInterface;
use App\Traits\ResponseAPI;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryService
{
    use ResponseAPI;

    protected CategoryRepositoryInterface $categoryRepositoryInterface;

    /**
     * @var Updater
     */
    private Updater $updater;
    /**
     * @var Creator
     */
    private Creator $creator;
    private $partnerCategoryRepositoryInterface;

    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface,PartnerCategoryRepositoryInterface $partnerCategoryRepositoryInterface, Creator $creator, Updater $updater)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->partnerCategoryRepositoryInterface = $partnerCategoryRepositoryInterface;
        $this->creator = $creator;
        $this->updater = $updater;
    }

    /**
     * @param $partner_id
     * @return \Illuminate\Http\JsonResponse
     * @throws CategoryNotFoundException
     */
    public function getCategoriesByPartner($partner_id)
    {
            $master_categories = $this->categoryRepositoryInterface->getCategoriesByPartner($partner_id);
            if($master_categories->isEmpty())
                throw new CategoryNotFoundException('কোন ক্যাটাগরি যোগ করা হয়নি!');
            $data = $this->makeData($master_categories,$partner_id);
            return $this->success("Successful", $data);
    }

    /**
     * @param $master_categories
     * @param $partner_id
     * @return array
     */
    public function makeData($master_categories, $partner_id)
    {
        $data = [];
        $data['total_category'] = count($master_categories);
        $data['categories'] = [];
        foreach ($master_categories as $category) {
            $item['id'] = $category->id;
            $item['name'] = $category->name;
            $item['is_published_for_sheba'] = $category->is_published_for_sheba;
            $total_services = 0;
            $category->children()->get()->each(function ($child) use ($partner_id, &$total_services) {
                $total_services += $child->products()->where('partner_id', $partner_id)->count();
            });
            $item['total_items'] = $total_services;
            array_push($data['categories'], $item);
        }
        return $data;

    }

    /**
     * @param CategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CategoryRequest $request)
    {
        $category =  $this->creator->setModifyBy($request->modifier)->setPartner($request->partner)->setName($request->name)->create();
        return $this->success("Successful", $category,201);
    }

    /**
     * @param CategoryRequest $request
     * @param $partner_id
     * @param $category_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CategoryRequest $request, $partner, $category)
    {
        $category = $this->categoryRepositoryInterface->find($category);
        if(!$category)
            throw new ModelNotFoundException();
        if($category->is_published_for_sheba)
        return $this->error("Not allowed to update this category", 403);
        $this->updater->setModifyBy($request->modifier)->setCategory($category)->setName($request->name)->update();
        return $this->success("Successful", $category,200);
    }

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($request)
    {
        $category_id = $request->category;
        $category = $this->categoryRepositoryInterface->where('id', $category_id)->with(['children' => function ($query) {
            $query->select('id','parent_id');
        }])->select('id')->first();
        if(!$category)
            return $this->error("Not Found", 404);
        if($category->is_published_for_sheba)
            return $this->error("Not allowed to delete this category", 403);
        $children = $category->children->pluck('id')->toArray();
        $master_cat_with_children = array_merge($children,[$category->id]);
        $this->categoryRepositoryInterface->whereIn('id',$master_cat_with_children)->delete();
        $this->partnerCategoryRepositoryInterface->whereIn('category_id',$master_cat_with_children)->delete();
        return $this->success("Successful", null,200,false);
    }


}

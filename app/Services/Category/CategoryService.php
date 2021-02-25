<?php namespace App\Services\Category;


use App\Http\Requests\CategoryRequest;
use App\Interfaces\CategoryRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Interfaces\PartnerCategoryRepositoryInterface;
use App\Traits\ResponseAPI;

class CategoryService
{
    use ResponseAPI;

    protected CategoryRepositoryInterface $categoryRepositoryInterface;
    protected CategoryRepository $categoryRepository;

    /**
     * @var Updater
     */
    private Updater $updater;
    /**
     * @var Creator
     */
    private Creator $creator;
    private $partnerCategoryRepositoryInterface;

    public function __construct(CategoryRepository $categoryRepository,CategoryRepositoryInterface $categoryRepositoryInterface,PartnerCategoryRepositoryInterface $partnerCategoryRepositoryInterface, Creator $creator, Updater $updater)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->partnerCategoryRepositoryInterface = $partnerCategoryRepositoryInterface;
        $this->creator = $creator;
        $this->updater = $updater;
        $this->categoryRepository = $categoryRepository;
    }

    public function getMasterCategoriesByPartner($partner_id)
    {
        try {
            $master_categories = $this->categoryRepositoryInterface->getMasterCategoriesByPartner($partner_id);
            if(!$master_categories)
                return $this->error("Not found",404);
            $data = $this->makeData($master_categories,$partner_id);
            return $this->success("Successful", $data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function makeData($master_categories,$partner_id)
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

    public function create(CategoryRequest $request)
    {
        $this->creator->setModifyBy($request->modifier)->setPartner($request->partner_id)->setName($request->name)->create();
        return $this->success("Successful", null,201);
    }

    public function update(CategoryRequest $request, $category_id)
    {
        $category = $this->categoryRepositoryInterface->find($category_id);
        if($category->is_published_for_sheba)
        return $this->error("Not allowed to update this category", 403);
        $this->updater->setModifyBy($request->modifier)->setCategory($category)->setName($request->name)->update();
    }

    public function delete($request)
    {
        $category_id = $request->category_id;
        $category = $this->categoryRepositoryInterface->where('id',$category_id)->with('children')->get();
        $children = $category->children()->pluck('id');
        $master_cat_with_children = array_merge($children,[$category->id]);
        if($category->is_published_for_sheba)
            return $this->error("Not allowed to delete this category", 403);
        $partner_category = $this->partnerCategoryRepositoryInterface->where('partner_id',$request->partner_id)->where('category_id', $category_id)->first();
        if(!$partner_category)
            return $this->error("Not Found", 404);

        $this->categoryRepositoryInterface->whereIn('id',$master_cat_with_children)->delete();
        $this->partnerCategoryRepositoryInterface->whereIn('category_id',$master_cat_with_children)->delete();
        return $this->success("Successful", null,201);
    }

    public function getCategory(){
        $category= $this->categoryRepositoryInterface->getCategory();
        return $this->success("Successful", $category,201);
    }
}

<?php namespace App\Services\Category;


use App\Http\Requests\CategoryRequest;
use App\Interfaces\CategoryRepositoryInterface;
use App\Traits\ResponseAPI;

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

    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface, Creator $creator, Updater $updater)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->creator = $creator;
        $this->updater = $updater;
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


}

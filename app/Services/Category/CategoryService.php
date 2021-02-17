<?php namespace App\Services\Category;


use App\Http\Requests\CategoryRequest;
use App\Interfaces\CategoryRepositoryInterface;
use App\Traits\ResponseAPI;

class CategoryService
{
    use ResponseAPI;

    protected CategoryRepositoryInterface $categoryRepositoryInterface;
    protected Creator $creator;

    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface, Creator $creator)
    {
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->creator = $creator;
    }

    public function create(CategoryRequest $request)
    {
        $this->creator->setPartner($request->user->id)->setName($request->name)->create();
        return $this->success("Successful", null,201);
    }


}

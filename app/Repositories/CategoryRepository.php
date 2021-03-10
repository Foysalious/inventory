<?php namespace App\Repositories;


use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use http\Env\Request;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }
    public function getCategoriesByPartner($partner_id)
    {
        return $this->model->leftJoin('category_partner', 'categories.id', '=', 'category_partner.category_id')
            ->where('category_partner.partner_id',$partner_id)
            ->whereNull('categories.parent_id')->get();
    }

    public function getCategory($partner_id)
    {
//        return $this->model->leftJoin('category_partner', 'categories.id', '=', 'category_partner.category_id')
//            ->where('category_partner.partner_id',$partner_id)
//            ->with(['children'=> function($q){
//                $q->select('id', 'name', 'parent_id');
//                }])->where('parent_id',NULL)->get();

        return $this->model->where(function ($q) use ($partner_id) {
                $q->where('is_published_for_sheba', 1)->orWhere(function ($q) use ($partner_id) {
                    $q->where('is_published_for_sheba', 0)->whereHas('categoryPartner', function ($q) use ($partner_id) {
                        $q->where('partner_id', $partner_id);
                    });
                });
            }) ->with(['children'=> function($q){
                $q->select('id', 'name', 'parent_id');
                }])->where('parent_id',NULL)->get();




//        try {
//            $partner_id = $request->partner->id;
//            $master_categories = PosCategory::where(function ($q) use ($partner_id) {
//                $q->where('is_published_for_sheba', 1)->orWhere(function ($q) use ($partner_id) {
//                    $q->where('is_published_for_sheba', 0)->whereHas('partnerPosCategory', function ($q) use ($partner_id) {
//                        $q->where('partner_id', $partner_id);
//                    });
//                });
//            })->with(['children' => function ($query) {
//                $query->select(array_merge($this->getSelectColumnsOfCategory(), ['parent_id']));
//            }])->parents()->published()->select($this->getSelectColumnsOfCategory())->get();
//
//            if (!$master_categories) return api_response($request, null, 404);
//
//            return api_response($request, $master_categories, 200, ['categories' => $master_categories]);
//        } catch (\Throwable $e) {
//            app('sentry')->captureException($e);
//            return api_response($request, null, 500);
//        }

    }


}

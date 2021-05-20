<?php namespace App\Repositories;


use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isEmpty;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function getCategoriesByPartner($partner_id)
    {
        return $this->model->where(function ($q) use ($partner_id) {
                $q->whereHas('categoryPartner', function ($q) use ($partner_id) {
                    $q->where('partner_id', $partner_id);
                });
        })->with('children', function ($q) {
            $q->leftJoin('category_partner', 'category_partner.category_id', '=', 'categories.id')
                ->select('categories.id', 'categories.name', 'categories.parent_id', 'categories.thumb as thumb', 'categories.is_published_for_sheba', 'category_partner.is_default');
        })->where('parent_id', NULL)->get();

    }

    public function getProductsByCategoryId($category_id)
    {

        return $this->model->where('id', $category_id)->get();
    }

    public function getCategoriesForWebstore($partner_id)
    {
        $master_categories = $this->model->where(function ($q) use ($partner_id) {
            $q->where('is_published_for_sheba', 1)->orWhere(function ($q) use ($partner_id) {
                $q->where('is_published_for_sheba', 0)->whereHas('categoryPartner', function ($q) use ($partner_id) {
                    $q->where('partner_id', $partner_id);
                })->whereHas('products',function ($q){
                    $q->select(DB::raw('SUM(id) as total_product'))
                        ->havingRaw('total_product > 0');
                });
            });
        })->select('id', 'name')->where('parent_id', NULL)->get();


        return $master_categories;


    }


}

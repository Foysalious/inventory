<?php namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    private const CATEGORY_PARTNER = 'categoryPartner';
    private const PARTNER_ID = 'partner_id';

    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function getCategoriesByPartner($partner_id, $updated_after = null)
    {
        $category_query =  $this->model->where(function ($q) use ($partner_id) {
            $q->whereHas(self::CATEGORY_PARTNER, function ($q) use ($partner_id) {
                $q->where(self::PARTNER_ID, $partner_id);
            });
        })->with('children', function ($q) {
            $q->leftJoin('category_partner', 'category_partner.category_id', '=', 'categories.id')
                ->select('categories.id', 'categories.name', 'categories.parent_id', 'categories.thumb as thumb', 'categories.is_published_for_sheba', 'category_partner.is_default');
        })->withCount('products')->where('parent_id', NULL);

        if($updated_after)
            $category_query = $category_query->where(function ($q) use ($updated_after) {
                $q->where('updated_at', '>=', $updated_after);
                $q->orWhere('created_at', '>=', $updated_after);
            });

        return $category_query->get();
    }

    public function getDeletedCategories( $partner_id, $updated_after)
    {
        $deleted_categories_query = $this->model->where(function ($q) use ($partner_id) {
            $q->whereHas(self::CATEGORY_PARTNER, function ($q) use ($partner_id) {
                $q->where(self::PARTNER_ID, $partner_id);
            });
        })->onlyTrashed();

        if($updated_after)
            $deleted_categories_query = $deleted_categories_query->where('deleted_at', '>=', $updated_after);
        return $deleted_categories_query->select('id')->get();
    }

    public function getProductsByCategoryId($category_id)
    {
        return $this->model->where('id', $category_id)->get();
    }

    public function getCategoriesForWebstore($partner_id)
    {
        return $this->model->where(function ($q) use ($partner_id) {
            $q->where('deleted_at', NULL)->whereHas(self::CATEGORY_PARTNER, function ($q) use ($partner_id) {
                $q->where(self::PARTNER_ID, $partner_id);
            })->whereHas('products', function ($q) {
                $q->select(DB::raw('SUM(id) as total_product'))
                    ->havingRaw('total_product > 0');
            });
        })->select('id', 'name')->where('parent_id', NULL)->get();
    }

    public function getDefaultSubCategory(int $partner_id, int $category_id)
    {
        return $this->model->where('parent_id', $category_id)
            ->whereHas(self::CATEGORY_PARTNER, function ($q) use ($partner_id) {
                $q->where(self::PARTNER_ID, $partner_id)->where('is_default', 1);
            })->first();
    }

    public function getSubCategoryIds(array $category_ids)
    {
        return $this->model->whereIn('parent_id', $category_ids)->select('id')->get();
    }
}

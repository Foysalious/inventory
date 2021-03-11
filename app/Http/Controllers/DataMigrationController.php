<?php namespace App\Http\Controllers;

use App\Http\Requests\DataMigrationRequest;
use App\Services\DataMigration\DataMigrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataMigrationController extends Controller
{
    private DataMigrationService $dataMigrationService;

    /**
     * DataMigrationController constructor.
     * @param DataMigrationService $dataMigrationService
     */
    public function __construct(DataMigrationService $dataMigrationService)
    {
        $this->dataMigrationService = $dataMigrationService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DataMigrationRequest $request
     * @return JsonResponse
     */
    public function store(DataMigrationRequest $request, $partner_id)
    {
        $partner_info = !is_array($request->partner_info) ? json_decode($request->partner_info,1) : $request->partner_info;
        $category_partner = !is_array($request->partner_pos_categories) ? json_decode($request->partner_pos_categories,1) : $request->partner_pos_categories;
        $categories = !is_array($request->pos_categories) ? json_decode($request->pos_categories,1) : $request->pos_categories;
        $products = !is_array($request->products) ? json_decode($request->products,1) : $request->products;
        $product_update_logs = !is_array($request->partner_pos_services_logs) ? json_decode($request->partner_pos_services_logs,1) : $request->partner_pos_services_logs;
        $this->dataMigrationService->setPartnerInfo($partner_info)
            ->setPartnerCategories($category_partner)
            ->setCategories($categories)
            ->setProducts($products)
            ->setProductUpdateLogs($product_update_logs)
            ->migrate();
        return $this->success('Successful', null);
    }
}

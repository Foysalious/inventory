<?php namespace App\Http\Controllers;

use App\Http\Requests\DataMigrationRequest;
use App\Http\Requests\PartnersUpdateRequest;
use App\Services\DataMigration\DataMigrationService;
use App\Services\Partner\PartnerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataMigrationController extends Controller
{
    private DataMigrationService $dataMigrationService;

    /**
     * DataMigrationController constructor.
     * @param DataMigrationService $dataMigrationService
     */
    public function __construct(DataMigrationService $dataMigrationService, protected PartnerService $partnerService)
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
        $partner_info = $this->formatData($request->partner_info);
        $category_partner = $this->formatData($request->partner_pos_categories);
        $categories = $this->formatData($request->pos_categories);
        $products = $this->formatData($request->products);
        $product_images = $this->formatData($request->partner_pos_services_image_gallery);
        $product_update_logs = $this->formatData($request->partner_pos_services_logs);
        $discounts = $this->formatData($request->partner_pos_service_discounts);

        $this->dataMigrationService->setPartnerInfo($partner_info)
            ->setPartnerCategories($category_partner)
            ->setCategories($categories)
            ->setProducts($products)
            ->setProductImages($product_images)
            ->setProductUpdateLogs($product_update_logs)
            ->setDiscounts($discounts)
            ->migrate();
        return $this->success('Successful', $partner_info);
    }

    private function formatData($data)
    {
        return !is_array($data) ? json_decode($data,1) : $data;
    }

    public function updatePartnersTable($partner_id, PartnersUpdateRequest $request)
    {
        return $this->partnerService->updatePartner($partner_id,$request);
    }
}

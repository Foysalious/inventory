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
    public function store(DataMigrationRequest $request)
    {
        $category_partner = !is_array($request->partner_pos_categories) ? json_decode($request->partner_pos_categories,1) : $request->partner_pos_categories;
        $categories = !is_array($request->pos_categories) ? json_decode($request->pos_categories,1) : $request->pos_categories;
        $this->dataMigrationService->setPartnerCategories($category_partner)->setCategories($categories)->migrate();
        return $this->success('Successful', null);
    }
}

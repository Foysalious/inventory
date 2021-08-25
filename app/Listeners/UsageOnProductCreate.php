<?php namespace App\Listeners;

use App\Events\ProductCreated;
use App\Services\Usage\Types;
use App\Services\Usage\UsageService;

class UsageOnProductCreate
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(protected UsageService $usageService){}

    /**
     * Handle the event.
     *
     * @param ProductCreated $event
     * @return void
     */
    public function handle(ProductCreated $event)
    {
        $this->usageService->setUserId($event->model->partner_id)->setUsageType(Types::INVENTORY_CREATE)->store();
    }
}

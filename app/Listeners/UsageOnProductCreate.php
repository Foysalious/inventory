<?php namespace App\Listeners;

use App\Events\ProductCreated;
use App\Jobs\Usage\UsageJob;
use App\Services\Usage\Types;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;

class UsageOnProductCreate
{
    use DispatchesJobs,SerializesModels;

    /**
     * Handle the event.
     *
     * @param ProductCreated $event
     * @return void
     */
    public function handle(ProductCreated $event)
    {
        $this->dispatch((new UsageJob((int) $event->model->partner_id, Types::INVENTORY_CREATE)));
    }
}

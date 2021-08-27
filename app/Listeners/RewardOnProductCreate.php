<?php namespace App\Listeners;

use App\Events\ProductCreated;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use App\Jobs\RewardOnProductCreate as RewardOnProductCreateJob;


class RewardOnProductCreate  {

    use DispatchesJobs,SerializesModels;


    /**
     * @param ProductCreated $event
     */
    public function handle(ProductCreated $event)
    {
        $this->dispatch((new RewardOnProductCreateJob($event->model)));
    }

}

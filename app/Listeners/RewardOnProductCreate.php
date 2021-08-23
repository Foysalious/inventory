<?php namespace App\Listeners;

use App\Events\RewardOnProductCreate as RewardOnProductCreateEvent;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use App\Jobs\RewardOnProductCreate as RewardOnProductCreateJob;


class RewardOnProductCreate  {

    use DispatchesJobs,SerializesModels;

    /**
     * @param RewardOnProductCreateEvent $event
     */
    public function handle(RewardOnProductCreateEvent $event)
    {
        $this->dispatch((new RewardOnProductCreateJob($event->model)));
    }

}

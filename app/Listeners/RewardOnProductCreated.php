<?php namespace App\Listeners;

use App\Events\RewardOnProductCreate;


use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;


class RewardOnProductCreated  {

    use DispatchesJobs,SerializesModels;

    /**
     * @param RewardOnProductCreate $event
     */
    public function handle(RewardOnProductCreate $event)
    {
        $this->dispatch((new RewardOnProductCreate($event->model)));
    }

}

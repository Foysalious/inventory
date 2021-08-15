<?php

namespace App\Jobs;


use App\Helper\Miscellaneous\RequestIdentification;
use App\Services\Product\ApiServerClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\SerializesModels;

class RewardOnProductCreate extends Job implements ShouldQueue
{
    protected $model;

    use InteractsWithQueue, SerializesModels;

    private const PRODUCT_CREATE_REWARD_EVENT_NAME = 'pos_inventory_create';
    private const PRODUCT_CREATE_REWARDABLE_TYPE = 'partner';

    public function __construct($model, protected ApiServerClient $apiServerClient)
    {
        $this->model = $model;
        $this->queue = 'reward';
    }

    public function handle()
    {
        $data = [
            'event' => self::PRODUCT_CREATE_REWARD_EVENT_NAME,
            'rewardable_type' => self::PRODUCT_CREATE_REWARDABLE_TYPE,
            'rewardable_id' => $this->model->partner_id,
            'event_data' => ['portal_name' => (new RequestIdentification())->get()['portal_name']]
        ];
        $this->apiServerClient->setBaseUrl()->post('pos/v1/reward/action', $data);
    }

    public function getJobId()
    {
        // TODO: Implement getJobId() method.
    }

    public function getRawBody()
    {
        // TODO: Implement getRawBody() method.
    }
}

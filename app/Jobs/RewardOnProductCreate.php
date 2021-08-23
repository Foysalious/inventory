<?php namespace App\Jobs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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

    public function __construct($model)
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
            //'event_data' => $this->model->apiRequest->portal_name
        ];
        try{
            $client = new Client();
            $client->post(config('sheba.api_url').'/pos/v1/reward/action',$data);
        }catch (GuzzleException $e){}

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

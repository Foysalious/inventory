<?php namespace App\Services\Channel;

use App\Http\Resources\ChannelResource;
use App\Services\BaseService;
use App\Interfaces\ChannelRepositoryInterface;


class ChannelService extends BaseService
{
    /**
     * @var ChannelRepositoryInterface
     */
    private ChannelRepositoryInterface $channelRepositoryInterface;

    public function __construct(ChannelRepositoryInterface $channelRepositoryInterface)
    {
        $this->channelRepositoryInterface = $channelRepositoryInterface;

    }

    public function getAll()
    {
        try {
            $resource = $this->channelRepositoryInterface->getAll();
            if ($resource->isEmpty()){
                return $this->error("There is no Channel", 404);
            }
                $channels = ChannelResource::collection($resource);
            return $this->success("Successful", ['channels' => $channels]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

}

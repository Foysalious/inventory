<?php namespace App\Services\Channel;
use App\Http\Resources\ChannelResource;
use App\Traits\ResponseAPI;
use App\Interfaces\ChannelRepositoryInterface;


class ChannelService
{
    use ResponseAPI;

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
            $channels = ChannelResource::collection($resource);
            return $this->success("Successful", $channels);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

}

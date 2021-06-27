<?php namespace App\Http\Controllers;

use App\Services\Channel\ChannelService;



class ChannelController extends Controller
{
    /** @var ChannelService */
    protected ChannelService $channelService;
    /**
     * @var ChannelService
     */


    public function __construct(ChannelService $channelService)
    {
        $this->channelService = $channelService;
    }

    public function index()
    {
        return $this->channelService->getAll();
    }

}

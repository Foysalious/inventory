<?php namespace App\Repositories;
use App\Interfaces\ChannelRepositoryInterface;
use App\Models\Channel;

class ChannelRepository extends BaseRepository implements ChannelRepositoryInterface
{
    public function __construct(Channel $model)
    {
        parent::__construct($model);
    }
}

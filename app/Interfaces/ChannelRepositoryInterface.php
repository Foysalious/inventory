<?php


namespace App\Interfaces;
use App\Providers\RepositoryServiceProvider;


interface ChannelRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll();
}

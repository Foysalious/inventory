<?php


namespace App\Interfaces;
use App\Providers\RepositoryServiceProvider;


interface UnitRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll();
}

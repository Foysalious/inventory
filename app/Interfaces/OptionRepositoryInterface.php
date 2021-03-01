<?php namespace App\Interfaces;


interface OptionRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllWithOptions($offset = 0, $limit = 50);
}

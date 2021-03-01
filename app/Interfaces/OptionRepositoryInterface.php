<?php namespace App\Interfaces;


interface OptionRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllWithOptions($partner_id,$offset = 0, $limit = 50);
}

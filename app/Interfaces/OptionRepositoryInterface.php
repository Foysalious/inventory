<?php namespace App\Interfaces;


interface OptionRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllWithOptions();
}

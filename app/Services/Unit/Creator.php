<?php namespace App\Services\Unit;

use App\Interfaces\UnitRepositoryInterface;

class Creator
{
    /** @var UnitRepositoryInterface */
    protected UnitRepositoryInterface $optionRepositoryInterface;
    protected string $name;

    public function __construct(UnitRepositoryInterface $unitRepositoryInterface)
    {
        $this->unitRepositoryInterface = $unitRepositoryInterface;

    }



}

<?php namespace App\Services\Sku;


use App\Interfaces\SkuRepositoryInterface;
use App\Models\Sku;

class Creator
{

    public function __construct(private SkuRepositoryInterface $skuRepository)
    {
    }


    /**
     * @param CreateSkuDto $createSkuDto
     * @return Sku
     */
    public function create(CreateSkuDto $createSkuDto): Sku
    {
        return $this->skuRepository->create($createSkuDto->toArray());
    }
}

<?php


namespace App\Services\Product;


use App\Interfaces\CombinationRepositoryInterface;

class CombinationCreator
{
    private $combinationRepository;
    private $combinationData;

    public function __construct(CombinationRepositoryInterface  $combinationRepository)
    {
        $this->combinationRepository = $combinationRepository;
    }

    public function setData($combinationData)
    {
       $this->combinationData =  $combinationData;
       return $this;
    }

    public function store()
    {
        return $this->combinationRepository->insert($this->combinationData);
    }

}

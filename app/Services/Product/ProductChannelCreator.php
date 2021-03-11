<?php namespace App\Services\Product;


use App\Interfaces\ProductChannelRepositoryInterface;
use App\Repositories\ProductChannelRepository;

class ProductChannelCreator
{
    private $productChannelData;
    private $productChannelRepository;

    /**
     * ProductChannelCreator constructor.
     * @param ProductChannelRepositoryInterface $productChannelRepository
     */
    public function __construct(ProductChannelRepositoryInterface $productChannelRepository)
    {
        $this->productChannelRepository = $productChannelRepository;
    }

    /**
     * @param mixed $productChannelData
     * @return ProductChannelCreator
     */
    public function setData($productChannelData)
    {
        $this->productChannelData = $productChannelData;
        return $this;
    }

    /**
     * @return mixed
     */
    public function store()
    {
        return $this->productChannelRepository->insert($this->productChannelData);
    }





}

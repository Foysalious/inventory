<?php namespace App\Services\Webstore\Collection;
use App\Interfaces\CategoryRepositoryInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use App\Interfaces\CollectionProductsRepositoryInterface;

class CollectionService
{
    use ResponseAPI;

    private CollectionProductsRepositoryInterface $collectionProductsRepositoryInterface;

    public function __construct(CollectionProductsRepositoryInterface $collectionProductsRepositoryInterface)
    {
        $this->collectionProductsRepositoryInterface = $collectionProductsRepositoryInterface;
    }
}

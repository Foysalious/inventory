<?php namespace App\Http\Controllers\Webstore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Webstore\Collection\CollectionService;

class CollectionController extends Controller
{
    /**
     * @var CollectionService
     */
    private CollectionService $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }
}

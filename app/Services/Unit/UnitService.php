<?php namespace App\Services\Unit;
use App\Http\Resources\UnitResource;
use App\Interfaces\UnitRepositoryInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;

class UnitService
{
    use ResponseAPI;

    /** @var UnitRepositoryInterface */
    protected UnitRepositoryInterface $unitRepositoryInterface;

    public function __construct(UnitRepositoryInterface $unitRepositoryInterface)
    {
        $this->unitRepositoryInterface = $unitRepositoryInterface;

    }

    public function getAll()
    {
        try {
            $resource = $this->unitRepositoryInterface->getAll();
            $units = UnitResource::collection($resource);
            return $this->success("Successful", $units);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}

<?php namespace App\Services\Option;

use App\Http\Resources\OptionResource;
use App\Interfaces\OptionRepositoryInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;

class OptionService
{
    use ResponseAPI;

    /** @var OptionRepositoryInterface */
    protected OptionRepositoryInterface $optionRepositoryInterface;
    /**  @var Creator */
    protected Creator $creator;

    public function __construct(OptionRepositoryInterface $optionRepositoryInterface, Creator $creator)
    {
        $this->optionRepositoryInterface = $optionRepositoryInterface;
        $this->creator = $creator;
    }

    public function getAll()
    {
        try {
            $resource = $this->optionRepositoryInterface->getAllWithOptions();
            $options = OptionResource::collection($resource);
            return $this->success("Successful", $options);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function create(Request $request)
    {
        $option = $this->creator->setName($request->name)->create();
        return $this->success("Successful", $option,201);
    }
}

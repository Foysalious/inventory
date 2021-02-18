<?php namespace App\Services\Option;

use App\Http\Requests\OptionRequest;
use App\Http\Resources\OptionResource;
use App\Interfaces\OptionRepositoryInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OptionService
{
    use ResponseAPI;

    /** @var OptionRepositoryInterface */
    protected OptionRepositoryInterface $optionRepositoryInterface;
    /**  @var Creator */
    protected Creator $creator;
    /**
     * @var Updater
     */
    protected Updater $updater;

    public function __construct(OptionRepositoryInterface $optionRepositoryInterface, Creator $creator, Updater $updater)
    {
        $this->optionRepositoryInterface = $optionRepositoryInterface;
        $this->creator = $creator;
        $this->updater = $updater;
    }

    /**
     * @return JsonResponse
     */
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

    /**
     * @param OptionRequest $request
     * @return JsonResponse
     */
    public function create(OptionRequest $request)
    {
        $option = $this->creator->setName($request->name)->create();
        return $this->success("Successful", $option,201);
    }

    /**
     * @param OptionRequest $request
     * @param $optionId
     * @return JsonResponse
     */
    public function update(OptionRequest $request, $optionId)
    {
        $option = $this->optionRepositoryInterface->find($optionId);
        $this->updater->setOption($option)->setName($request->name)->update();
        return $this->success("Successful", $option,200);
    }
}

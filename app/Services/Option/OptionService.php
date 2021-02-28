<?php namespace App\Services\Option;

use App\Exceptions\OptionNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\OptionRequest;
use App\Http\Resources\OptionResource;
use App\Interfaces\OptionRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OptionService extends Controller
{
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
     * @throws OptionNotFoundException
     */
    public function getAll()
    {
        $resource = $this->optionRepositoryInterface->getAllWithOptions();
        $options = OptionResource::collection($resource);
        if ($options->isEmpty()) throw new OptionNotFoundException('আপনার কোন ভেরিয়েসন এড করা নেই!');
        return $this->success("Successful", $options);
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
        $option = $this->optionRepositoryInterface->findOrFail($optionId);
        if(!$option) throw new ModelNotFoundException();
        $this->updater->setOption($option)->setName($request->name)->update();
        return $this->success("Successful", $option,200);
    }
}

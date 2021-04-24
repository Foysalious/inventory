<?php namespace App\Services\Value;

use App\Http\Requests\ValueRequest;
use App\Http\Requests\ValueUpdateRequest;
use App\Interfaces\ValueRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Http\JsonResponse;

class ValueService extends BaseService
{
    /** @var ValueRepositoryInterface */
    protected ValueRepositoryInterface $valueRepositoryInterface;
    /** @var Creator */
    protected Creator $creator;
    /** @var Updater */
    protected Updater $updater;

    /**
     * ValueService constructor.
     * @param ValueRepositoryInterface $valueRepositoryInterface
     * @param Creator $creator
     * @param Updater $updater
     */
    public function __construct(ValueRepositoryInterface $valueRepositoryInterface, Creator $creator, Updater $updater)
    {
        $this->valueRepositoryInterface = $valueRepositoryInterface;
        $this->creator = $creator;
        $this->updater = $updater;
    }

    /**
     * @param ValueRequest $request
     * @param $option
     * @return JsonResponse
     */
    public function create(ValueRequest $request, $option)
    {
        $this->creator->setOptionId($option)->setValues($request->values)->create();
        return $this->success("Successful", null,201);
    }

    /**
     * @param ValueUpdateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ValueUpdateRequest $request, $id)
    {
        $value = $this->valueRepositoryInterface->findOrFail($id);
        $this->updater->setValue($value)->setName($request->name)->update();
        return $this->success("Successful", ['value' => $value],200);
    }

    public function  delete($id)
    {
        try {
            $value = $this->valueRepositoryInterface->findOrFail($id);
            $value_id=$value->id;
            $this->valueRepositoryInterface->where('id', $value_id)->delete();
            return $this->success("Successful", ['value' => $value],200, false);
        }
        catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }

    }
}

<?php namespace App\Services\Value;

use App\Http\Requests\ValueRequest;
use App\Http\Requests\ValueUpdateRequest;
use App\Interfaces\ValueRepositoryInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ValueService
{
    use ResponseAPI;

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
        return $this->success("Successful", $value,200);
    }
}

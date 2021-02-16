<?php namespace App\Services\Value;

use App\Http\Requests\OptionRequest;
use App\Http\Requests\ValueRequest;
use App\Interfaces\ValueRepositoryInterface;
use App\Traits\ResponseAPI;
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

    public function __construct(ValueRepositoryInterface $valueRepositoryInterface, Creator $creator, Updater $updater)
    {
        $this->valueRepositoryInterface = $valueRepositoryInterface;
        $this->creator = $creator;
        $this->updater = $updater;
    }

    public function create(ValueRequest $request, $option)
    {
        $this->creator->setOptionId($option)->setValues($request->values)->create();
        return $this->success("Successful", null,201);
    }

    public function update(Request $request, $id)
    {
        $value = $this->valueRepositoryInterface->find($id);
        $this->updater->setValue($value)->setName($request->name)->update();
        return $this->success("Successful", $value,200);
    }
}

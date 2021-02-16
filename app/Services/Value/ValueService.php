<?php namespace App\Services\Value;

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

    public function __construct(ValueRepositoryInterface $valueRepositoryInterface, Creator $creator)
    {
        $this->valueRepositoryInterface = $valueRepositoryInterface;
        $this->creator = $creator;
    }

    public function create(ValueRequest $request, $option)
    {
        $this->creator->setOptionId($option)->setValues(json_decode($request->values))->create();
        return $this->success("Successful", null,201);
    }
}

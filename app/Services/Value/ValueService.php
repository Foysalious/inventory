<?php namespace App\Services\Value;

use App\Http\Resources\ValueResource;
use App\Interfaces\ValueRepositoryInterface;
use App\Models\Option;
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

    public function create(Request $request, $option)
    {
        $values = $this->creator->setOptionId($option)->setValues(json_decode($request->values))->create();
        return $this->success("Successful", $values,201);
    }
}

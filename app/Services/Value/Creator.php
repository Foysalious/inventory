<?php namespace App\Services\Value;

use App\Interfaces\ValueRepositoryInterface;
use App\Models\Option;

class Creator
{
    /** @var ValueRepositoryInterface */
    protected ValueRepositoryInterface $valuesRepositoryInterface;
    protected array $values;
    protected int $optionId;

    public function __construct(ValueRepositoryInterface $valuesRepositoryInterface)
    {
        $this->valuesRepositoryInterface = $valuesRepositoryInterface;
    }

    public function setOptionId($optionId)
    {
        $this->optionId = $optionId;
        return $this;
    }

    public function setValues(array $values)
    {
        $this->values = $values;
        return $this;
    }

    public function create()
    {
        return $this->valuesRepositoryInterface->insert($this->makeValues($this->values));
    }

    private function makeValues($values)
    {
        $formatted_values = [];
        foreach ($values as $value)
        {
            array_push($formatted_values, [
                'option_id' => $this->optionId,
                'name' => $value
            ]);
        }
        return $formatted_values;
    }

}

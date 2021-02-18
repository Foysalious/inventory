<?php namespace App\Services\Value;


use App\Interfaces\ValueRepositoryInterface;
use App\Models\Value;

class Updater
{
    /** @var ValueRepositoryInterface */
    protected ValueRepositoryInterface $valuesRepositoryInterface;
    protected int $optionId;
    /**  @var Value */
    protected Value $value;
    protected $name;

    public function __construct(ValueRepositoryInterface $valuesRepositoryInterface)
    {
        $this->valuesRepositoryInterface = $valuesRepositoryInterface;
    }

    public function setValue(Value $value)
    {
        $this->value = $value;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function update()
    {
        return $this->valuesRepositoryInterface->update($this->value, $this->makeData());
    }

    private function makeData()
    {
        return [
            'name' => $this->name
        ];
    }
}

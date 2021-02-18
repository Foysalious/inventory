<?php namespace App\Services\Option;

use App\Interfaces\OptionRepositoryInterface;

class Creator
{
    /** @var OptionRepositoryInterface */
    protected OptionRepositoryInterface $optionRepositoryInterface;
    protected string $name;

    public function __construct(OptionRepositoryInterface $optionRepositoryInterface)
    {
        $this->optionRepositoryInterface = $optionRepositoryInterface;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function create()
    {
        return $this->optionRepositoryInterface->create($this->makeData());
    }

    public function makeData()
    {
        return [
            'name' => $this->name
        ];
    }

}

<?php namespace App\Services\Option;


use App\Interfaces\OptionRepositoryInterface;
use App\Models\Option;

class Updater
{
    /**
     * @var OptionRepositoryInterface
     */
    protected OptionRepositoryInterface $optionRepositoryInterface;
    protected $name;
    /**
     * @var Option
     */
    protected Option $option;

    public function __construct(OptionRepositoryInterface $optionRepositoryInterface)
    {
        $this->optionRepositoryInterface = $optionRepositoryInterface;
    }

    public function setOption(Option $option)
    {
        $this->option = $option;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function update()
    {
        return $this->optionRepositoryInterface->update($this->option, $this->makeData());
    }

    public function makeData()
    {
        return [
            'name' => $this->name
        ];
    }
}

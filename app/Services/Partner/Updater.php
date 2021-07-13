<?php namespace App\Services\Partner;

use App\Models\Partner;

class Updater
{
    protected ?string $sub_domain;
    protected ?float $vat_percentage;
    protected Partner $partner;

    /**
     * @param string|null $sub_domain
     * @return $this
     */
    public function setSubDomain(?string $sub_domain)
    {
        $this->sub_domain = $sub_domain;
        return $this;
    }

    /**
     * @param float|null $vat_percentage
     * @return $this
     */
    public function setVatPercentage(?float $vat_percentage)
    {
        $this->vat_percentage = $vat_percentage;
        return $this;
    }

    /**
     * @param Partner $partner
     * @return $this
     */
    public function setPartner(Partner $partner)
    {
        $this->partner = $partner;
        return $this;
    }

    public function update()
    {
        if(!is_null($this->sub_domain)){
            $this->partner->sub_domain = $this->sub_domain;
        }
        if(!is_null($this->vat_percentage)){
            $this->partner->vat_percentage = $this->vat_percentage;
        }
        $this->partner->save();
    }
}

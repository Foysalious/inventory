<?php namespace App\Services\Partner;

use App\Models\Partner;
use App\Traits\ModificationFields;

class Updater
{
    use ModificationFields;
    protected PartnerDto $partnerDto;
    protected Partner $partner;

    /**
     * @param Partner $partner
     * @return $this
     */
    public function setPartner(Partner $partner)
    {
        $this->partner = $partner;
        return $this;
    }

    /**
     * @param mixed $partnerDto
     */
    public function setPartnerDto(PartnerDto $partnerDto)
    {
        $this->partnerDto = $partnerDto;
        return $this;
    }

    public function update()
    {
        $this->partner->sub_domain = $this->partnerDto->sub_domain ?? $this->partner->sub_domain;
        $this->partner->vat_percentage = $this->partnerDto->vat_percentage ?? $this->partner->vat_percentage;
        $this->partner->save($this->withUpdateModificationField([]));
    }
}

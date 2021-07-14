<?php namespace App\Services\Partner;

use App\Http\Requests\PartnerUpdateRequest;
use App\Repositories\PartnerRepository;
use App\Services\BaseService;

class PartnerService extends  BaseService
{
    public function __construct(
        protected Updater $partnerUpdater,
        protected PartnerRepository $partnerRepository
    )
    {
    }

    public function updatePartner(int $partner_id, PartnerUpdateRequest $request)
    {
        $partner = $this->partnerRepository->where('id', $partner_id)->first();
        if(!$partner) {
            return $this->error("Bad Request", 400);
        }
        $this->partnerUpdater->setPartner($partner)
            ->setSubDomain($request->sub_domain ?? null)
            ->setVatPercentage($request->vat_percentage ?? null)
            ->update();
        return $this->success('successful', [], 200);
    }
}

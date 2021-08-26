<?php namespace App\Services\Product;


use App\Exceptions\AuthorizationException;
use App\Interfaces\PartnerRepositoryInterface;
use App\Services\AccessManager\AccessManager;
use App\Services\AccessManager\Features;

class CheckProductPublishAccess
{
    public function __construct(private AccessManager $accessManager, private PartnerRepositoryInterface $partnerRepository){}

    /**
     * @throws AuthorizationException
     */
    public function check($partnerId)
    {
        $this->accessManager
            ->setPartnerId($partnerId)
            ->setFeature(Features::PRODUCT_WEBSTORE_PUBLISH)
            ->setProductPublishedCount($this->partnerRepository->getPartnerPublishedProductsCount($partnerId))
            ->checkAccess();
    }
}

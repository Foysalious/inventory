<?php namespace App\Repositories\Accounting;

use App\Services\Accounting\Constants\UserType;
use App\Services\Accounting\Exceptions\AccountingEntryServerError;

class AccountingRepository extends BaseRepository
{
    /**
     * @param int $partner_id
     * @param array $data
     * @return mixed
     * @throws AccountingEntryServerError
     */
    public function storeEntry(array $data, int $partner_id)
    {
        $url = "api/entries/";
        try {
            return $this->client->setUserType(UserType::PARTNER)->setUserId($partner_id)->post($url, $data);
        } catch (AccountingEntryServerError $e) {
            throw new AccountingEntryServerError($e->getMessage(), $e->getCode());
        }
    }

}

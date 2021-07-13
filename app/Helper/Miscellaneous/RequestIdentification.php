<?php namespace App\Helper\Miscellaneous;

use App\Traits\ModificationFields;

class RequestIdentification
{
    use ModificationFields;

    /**
     * Merge the data with only user agent modification fields.
     *
     * @param $data
     * @return array
     */
    public function set($data)
    {
        return array_merge($data, $this->get());
    }

    public function get()
    {
        define('PORTAL_NAME', 'portal_name');
        $created_by_type = $this->getData();

        return [
            PORTAL_NAME => $this->getPortalName(),
            'ip' => !is_null(request('ip')) ? request('ip') : getIp(),
            'user_agent' => !is_null(request('user_agent')) ? request('user_agent') : request()->header('User-Agent'),
            'created_by_type' => $created_by_type[0] ? $created_by_type : 'automatic'
        ];
    }

    private function getPortalName()
    {
        if (request()->hasHeader('Portal-Name')) {
            return request()->header('Portal-Name');
        } elseif (!is_null(request('PORTAL_NAME'))) {
            return request('PORTAL_NAME');
        } else {
            return config('sheba.portal');
        }
    }
}

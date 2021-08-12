<?php namespace App\Services\Product;

use App\Services\ClientServer\BaseClientServer;


class ApiServerClient extends BaseClientServer
{
    public function setBaseUrl()
    {
        $this->baseUrl = rtrim(config('sheba.api_url'), '/');
        return $this;
    }
}

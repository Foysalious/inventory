<?php namespace App\Services\Webstore;
use App\Exceptions\HttpException;
use Throwable;

class PosServerError
{
    public function __construct($message = "", $code = 402, Throwable $previous = null)
    {
        if (!$message || $message == "") {
            $message = 'Inventory Service server not working as expected.';
        }
        parent::__construct($message, $code, $previous);

    }
}

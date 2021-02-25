<?php namespace App\Exceptions;


use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OptionNotFoundException extends Exception
{
    public function __construct($message = 'Option Not Found', $code = Response::HTTP_NOT_FOUND, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

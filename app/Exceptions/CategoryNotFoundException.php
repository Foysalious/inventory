<?php namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


class CategoryNotFoundException extends BaseException
{
    public function __construct($message = 'Category Not Found', $code = Response::HTTP_NOT_FOUND, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}

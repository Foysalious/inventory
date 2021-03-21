<?php namespace App\Exceptions;


use Symfony\Component\HttpFoundation\Response;

class ProductDetailsPropertyValidationError extends BaseException
{
    public function __construct($message = 'Product details structure is not corect', $code = Response::HTTP_NOT_FOUND, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}

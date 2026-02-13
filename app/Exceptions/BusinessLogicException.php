<?php

namespace App\Exceptions;
use Throwable;
use Exception;

class BusinessLogicException extends Exception
{
    public function __construct(string $message = "Business logic error", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 
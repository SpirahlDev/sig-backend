<?php

namespace App\Exceptions;
use Throwable;
use Exception;

/**
 * Une petite classe d'exeption qui me permet de faire la difference entre une exception technique et une exception metier.
 */
class BusinessLogicException extends Exception
{
    public function __construct(string $message = "Business logic error", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 
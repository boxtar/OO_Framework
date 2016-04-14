<?php

namespace App\Core\Exceptions;


class NoActionFoundException extends \Exception
{
    public function __construct($message, $code = 500, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
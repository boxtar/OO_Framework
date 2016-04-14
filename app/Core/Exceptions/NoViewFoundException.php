<?php
/**
 * Created by PhpStorm.
 * User: johnpaul
 * Date: 14/04/2016
 * Time: 21:21
 */

namespace App\Core\Exceptions;


class NoViewFoundException extends \Exception
{
    public function __construct($message, $code = 500, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
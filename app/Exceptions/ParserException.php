<?php

namespace App\Exceptions;

use Exception;

/**
 * Class ParserException
 * @package App\Modules\Auth\Exceptions
 */
class ParserException extends Exception
{
    /**
     * ParserException constructor.
     * @param $code
     * @param $message
     */
    public function __construct($code, $message)
    {
        parent::__construct($code, $message);
    }
}

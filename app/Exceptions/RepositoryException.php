<?php

namespace App\Exceptions;

use Exception;

/**
 * Class RepositoryException
 * @package App\Exceptions
 */
class RepositoryException extends Exception
{
    /**
     * RepositoryException constructor.
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}

<?php

namespace App\Exceptions;

use Exception;

class InvalidStatusTransitionException extends Exception
{
    public function __construct(string $message = 'Invalid status transition requested for this record.')
    {
        parent::__construct($message, 422);
    }
}

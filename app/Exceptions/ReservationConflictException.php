<?php

namespace App\Exceptions;

use Exception;

class ReservationConflictException extends Exception
{
    public function __construct(string $message = 'The selected room is no longer available for the requested date range.')
    {
        parent::__construct($message, 409);
    }
}

<?php

namespace App\Exceptions;

use Exception;

class UnsettledBalanceException extends Exception
{
    public function __construct(string $message = 'Cannot check out guest with an outstanding folio balance. Please settle balance first.')
    {
        parent::__construct($message, 422);
    }
}

<?php

namespace App\Exceptions;

use Exception;

class OrderException extends Exception
{
    public static function tableNotAvailable(): self
    {
        return new self('Table is not available for ordering', 422);
    }

    public static function emptyCart(): self
    {
        return new self('Cannot create order with empty cart', 422);
    }

    public static function invalidStatus(string $from, string $to): self
    {
        return new self("Cannot transition order from {$from} to {$to}", 422);
    }

    public static function alreadyPaid(): self
    {
        return new self('Order is already paid', 422);
    }

    public static function cannotModifyOrder(): self
    {
        return new self('Cannot modify order in current status', 422);
    }
}

<?php

namespace App\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
    public function __construct(
        string $message = 'An unexpected error occurred.',
        ?int $code = 0,
    ) {
        parent::__construct($message, $code);
    }

    abstract public function getDisplayMessage(): string;

    abstract public function getDisplayCode(): int;
}

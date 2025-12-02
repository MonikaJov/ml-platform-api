<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
    abstract public function getDisplayMessage(): string;

    abstract public function getDisplayCode(): int;
}

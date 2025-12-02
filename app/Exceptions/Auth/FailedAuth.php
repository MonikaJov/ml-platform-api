<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class FailedAuth extends Exception
{
    private const int DEFAULT_HTTP_CODE = Response::HTTP_BAD_REQUEST;

    /**
     * Render the exception into an HTTP JSON response.
     */
    public function render(): JsonResponse
    {
        return response()->json(['error' => $this->getDisplayMessage()], $this->getDisplayCode());
    }

    public function getDisplayMessage(): string
    {
        return $this->getMessage() !== '' ? $this->getMessage() : $this->defaultMessage();
    }

    public function getDisplayCode(): int
    {
        return $this->getCode() !== 0 ? $this->getCode() : self::DEFAULT_HTTP_CODE;
    }

    private function defaultMessage(): string
    {
        return Response::$statusTexts[self::DEFAULT_HTTP_CODE];
    }
}

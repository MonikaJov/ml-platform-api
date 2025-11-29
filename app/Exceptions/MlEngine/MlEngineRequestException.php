<?php

namespace App\Exceptions\MlEngine;

use App\Exceptions\BaseException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MlEngineRequestException extends BaseException
{
    private const int DEFAULT_HTTP_CODE = Response::HTTP_BAD_GATEWAY;

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => $this->getDisplayMessage(),
            'details' => $this->getMessage(),
        ], $this->getDisplayCode());
    }

    public function getDisplayMessage(): string
    {
        return $this->defaultMessage();
    }

    public function getDisplayCode(): int
    {
        return $this->getCode() ?: self::DEFAULT_HTTP_CODE;
    }

    private function defaultMessage(): string
    {
        return __('ML engine returned an error.');
    }
}

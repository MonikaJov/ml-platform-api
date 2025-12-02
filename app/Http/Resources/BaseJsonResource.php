<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseJsonResource extends JsonResource
{
    protected int $statusCode = Response::HTTP_OK;

    public function withStatus(int $status): static
    {
        $this->statusCode = $status;

        return $this;
    }

    public function withResponse(Request $request, JsonResponse $response): void
    {
        $response->setStatusCode($this->statusCode);
    }
}

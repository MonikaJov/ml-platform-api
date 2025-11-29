<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class BaseJsonResource extends JsonResource
{
    protected int $statusCode = Response::HTTP_OK;

    public function withStatus(int $status): static
    {
        $this->statusCode = $status;

        return $this;
    }

    public function withResponse($request, $response): void
    {
        $response->setStatusCode($this->statusCode);
    }
}

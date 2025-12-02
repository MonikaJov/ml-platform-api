<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;

final class SuccessfulOperationMessageResource extends BaseJsonResource
{
    private string $message;

    private int $code;

    private ?array $details;

    public function __construct(mixed $resource)
    {
        $this->message = $resource['message'];
        $this->code = $resource['code'];
        $this->details = $resource['details'] ?? null;

        parent::__construct($resource);
    }

    /** @return array<string, string|int> */
    public function toArray(Request $request): array
    {
        return [
            'message' => $this->message,
            'code' => $this->code,
            'details' => $this->details ?? null,
        ];
    }
}

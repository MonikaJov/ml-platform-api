<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class SuccessfulOperationMessageResource extends BaseJsonResource
{
    public string $message;

    public int $code;

    public ?array $details;

    public function __construct($resource)
    {
        $this->message = $resource['message'];
        $this->code = $resource['code'];
        $this->details = $resource['details'] ?? null;

        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'message' => $this->message,
            'code' => $this->code,
            'details' => $this->details ?? null,
        ];
    }
}

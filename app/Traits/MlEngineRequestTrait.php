<?php

namespace App\Traits;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

trait MlEngineRequestTrait
{
    protected function mlHeaders(): array
    {
        return [
            config('app.ml_platform_internal_auth.header') => config('app.ml_platform_internal_auth.token'),
        ];
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    protected function postToMlEngine(string $url, array $data, $fileHandle = null, ?string $fileName = null): Response
    {
        $request = Http::acceptJson()
            ->withHeaders($this->mlHeaders());

        if ($fileHandle && $fileName) {
            $request = $request->attach('dataset', $fileHandle, $fileName);
        }

        return $request->post($url, $data)->throw();
    }
}

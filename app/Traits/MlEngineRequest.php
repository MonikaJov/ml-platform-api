<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

trait MlEngineRequest
{
    /** @return array<string, string> */
    protected function mlHeaders(): array
    {
        return [
            config('app.ml_platform_internal_auth.header') => config('app.ml_platform_internal_auth.token'),
        ];
    }

    /**
     * @param  array<int, string>  $data
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    protected function postToMlEngine(string $url, array $data, mixed $fileHandle = null, ?string $fileName = null): Response
    {
        $request = Http::acceptJson()
            ->withHeaders($this->mlHeaders());

        if ($fileHandle && $fileName) {
            $request = $request->attach('dataset', $fileHandle, $fileName);
        }

        return $request->post($url, $data)->throw();
    }
}

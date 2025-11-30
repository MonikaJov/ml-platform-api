<?php

namespace App\Actions\BestModel;

use App\Exceptions\MlEngine\MlEngineResponseException;
use App\Http\Resources\SuccessfulOperationMessageResource;
use App\Models\Dataset;
use App\Models\ProblemDetail;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\Response;

class StartTrainingModel
{
    use AsAction;

    /**
     * @throws RequestException
     * @throws ConnectionException
     * @throws MlEngineResponseException
     */
    public function handle(Dataset $dataset, ProblemDetail $problemDetail): SuccessfulOperationMessageResource
    {
        return $this->submitTrainingJob($dataset->getFullPath(), $problemDetail);
    }

    private function trainUrl(): string
    {
        return config('app.ml_api_url').config('app.endpoints.train');
    }

    private function mlHeaders(): array
    {
        return [
            config('app.ml_platform_internal_auth.header') => config('app.ml_platform_internal_auth.token'),
        ];
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     * @throws MlEngineResponseException
     */
    private function submitTrainingJob(string $fullPath, ProblemDetail $problemDetail): SuccessfulOperationMessageResource
    {
        $handle = fopen($fullPath, 'r');

        try {
            $response = Http::acceptJson()
                ->withHeaders($this->mlHeaders())
                ->attach('dataset', $handle, basename($fullPath))
                ->post($this->trainUrl(), [
                    'target_column' => $problemDetail->target_column,
                    'problem_type' => $problemDetail->type->value,
                ])
                ->throw();

            $taskId = data_get($response->json(), 'details.task_id');

            if (! $taskId) {
                throw new MlEngineResponseException(
                    "ML API didn't return a valid task_id. Response: ".json_encode($response->json())
                );
            }

            $problemDetail->update(['task_id' => $taskId]);

            return SuccessfulOperationMessageResource::make($response->json())
                ->withStatus(Response::HTTP_ACCEPTED);
        } finally {
            fclose($handle);
        }
    }
}

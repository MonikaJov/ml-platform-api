<?php

namespace App\Actions\BestModel;

use App\Exceptions\MlEngine\MlEngineResponseException;
use App\Http\Resources\SuccessfulOperationMessageResource;
use App\Models\Dataset;
use App\Models\ProblemDetail;
use App\Traits\MlEngineRequestTrait;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\Response;

class StartTrainingModel
{
    use AsAction, MlEngineRequestTrait;

    /**
     * @throws RequestException
     * @throws ConnectionException
     * @throws MlEngineResponseException
     */
    public function handle(Dataset $dataset, ProblemDetail $problemDetail): SuccessfulOperationMessageResource
    {
        return $this->submitTrainingJob($dataset->getFullPath(), $problemDetail);
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
            $response = $this->postToMlEngine($this->trainUrl(), [
                'target_column' => $problemDetail->target_column,
                'problem_type' => $problemDetail->type->value,
            ], $handle, basename($fullPath));

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

    private function trainUrl(): string
    {
        return config('app.ml_api_url').config('app.endpoints.train');
    }
}

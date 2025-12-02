<?php

declare(strict_types=1);

namespace App\Actions\BestModel;

use App\Exceptions\MlEngine\MlEngineResponse;
use App\Http\Resources\SuccessfulOperationMessageResource;
use App\Models\Dataset;
use App\Models\ProblemDetail;
use App\Traits\MlEngineRequest;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\Response;

final class StartTrainingModel
{
    use AsAction, MlEngineRequest;

    /**
     * @throws RequestException
     * @throws ConnectionException
     * @throws MlEngineResponse
     */
    public function handle(Dataset $dataset, ProblemDetail $problemDetail): SuccessfulOperationMessageResource
    {
        return $this->submitTrainingJob($dataset->full_path, $problemDetail);
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     * @throws MlEngineResponse
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
                throw new MlEngineResponse(
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

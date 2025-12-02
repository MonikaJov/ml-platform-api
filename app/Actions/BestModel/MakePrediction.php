<?php

declare(strict_types=1);

namespace App\Actions\BestModel;

use App\Exceptions\MlEngine\MlEngineResponse;
use App\Http\Requests\BestModel\MakePredictionRequest;
use App\Http\Resources\BestModel\PredictionResource;
use App\Models\BestModel;
use App\Models\ProblemDetail;
use App\Traits\MlEngineRequest;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Lorisleiva\Actions\Concerns\AsAction;

final class MakePrediction
{
    use AsAction, MlEngineRequest;

    /**
     * @throws RequestException
     * @throws ConnectionException
     * @throws MlEngineResponse
     */
    public function handle(MakePredictionRequest $request, ProblemDetail $problemDetail, BestModel $bestModel): PredictionResource
    {
        $response = $this->postToMlEngine($this->predictionUrl(), [
            'record' => $request->validated()['data'],
            'model_path' => $bestModel->path,
        ]);

        $predicted_value = data_get($response->json(), 'prediction');

        if (! array_key_exists('prediction', $response->json()) || is_null($predicted_value)) {
            throw new MlEngineResponse(
                "ML API didn't return a valid prediction. Response: ".json_encode($response->json())
            );
        }

        return PredictionResource::make([
            'predicted_value' => $predicted_value,
            'target_column' => $problemDetail->target_column,
        ]);
    }

    private function predictionUrl(): string
    {
        return config('app.ml_api_url').config('app.endpoints.predict');
    }
}

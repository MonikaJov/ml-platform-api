<?php

namespace App\Actions\BestModel;

use App\Exceptions\MlEngine\MlEngineResponseException;
use App\Http\Requests\BestModel\MakePredictionRequest;
use App\Http\Resources\BestModel\PredictionResource;
use App\Models\BestModel;
use App\Models\ProblemDetail;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class MakePrediction
{
    use AsAction;

    /**
     * @throws RequestException
     * @throws ConnectionException
     * @throws MlEngineResponseException
     */
    public function handle(MakePredictionRequest $request, ProblemDetail $problemDetail, BestModel $bestModel): PredictionResource
    {

        $response = Http::acceptJson()
            ->withHeaders([
                config('app.ml_platform_internal_auth.header') => config('app.ml_platform_internal_auth.token'),
            ])
            ->post($this->predictionUrl(), [
                'record' => $request->validated()['data'],
                'model_path' => $bestModel->path,
            ])
            ->throw();

        $predicted_value = data_get($response->json(), 'prediction');

        if (! array_key_exists('prediction', $response->json()) || is_null($predicted_value)) {
            throw new MlEngineResponseException(
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

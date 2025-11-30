<?php

namespace App\Http\Controllers\BestModel;

use App\Actions\BestModel\MakePrediction;
use App\Exceptions\MlEngine\MlEngineConnectionException;
use App\Exceptions\MlEngine\MlEngineRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\BestModel\MakePredictionRequest;
use App\Http\Resources\BestModel\PredictionResource;
use App\Models\BestModel;
use App\Models\Dataset;
use App\Models\ProblemDetail;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class MakePredictionController extends Controller
{
    /**
     *  api.dataset.problem-detail.best-model.predict
     *
     * @throws MlEngineRequestException|MlEngineConnectionException
     */
    public function __invoke(MakePredictionRequest $request, Dataset $dataset, ProblemDetail $problemDetail, BestModel $bestModel): PredictionResource
    {
        try {
            return MakePrediction::run($request, $problemDetail, $bestModel);
        } catch (ConnectionException $e) {
            throw new MlEngineConnectionException($e->getMessage());
        } catch (RequestException $e) {
            throw new MlEngineRequestException($e->response?->json('error') ?? $e->getMessage(), $e->response?->status());
        }
    }
}

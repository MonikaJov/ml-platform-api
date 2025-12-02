<?php

declare(strict_types=1);

namespace App\Http\Controllers\BestModel;

use App\Actions\BestModel\MakePrediction;
use App\Exceptions\MlEngine\MlEngineConnection;
use App\Exceptions\MlEngine\MlEngineRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\BestModel\MakePredictionRequest;
use App\Http\Resources\BestModel\PredictionResource;
use App\Models\BestModel;
use App\Models\Dataset;
use App\Models\ProblemDetail;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

final class MakePredictionController extends Controller
{
    /**
     *  api.dataset.problem-detail.best-model.predict
     *
     * @throws MlEngineRequest|MlEngineConnection
     */
    public function __invoke(MakePredictionRequest $request, Dataset $dataset, ProblemDetail $problemDetail, BestModel $bestModel): PredictionResource
    {
        try {
            return MakePrediction::run($request, $problemDetail, $bestModel);
        } catch (ConnectionException $e) {
            throw new MlEngineConnection($e->getMessage());
        } catch (RequestException $e) {
            throw new MlEngineRequest($e->response?->json('error') ?? $e->getMessage(), $e->response?->status());
        }
    }
}

<?php

namespace App\Http\Controllers\BestModel;

use App\Actions\BestModel\StartTrainingModel;
use App\Exceptions\MlEngine\MlEngineConnectionException;
use App\Exceptions\MlEngine\MlEngineRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\SuccessfulOperationMessageResource;
use App\Models\Dataset;
use App\Models\ProblemDetail;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class StartTrainingModelController extends Controller
{
    /**
     * api.dataset.problem-detail.best-models.train
     *
     * @throws MlEngineRequestException|MlEngineConnectionException
     */
    public function __invoke(Dataset $dataset, ProblemDetail $problemDetail): SuccessfulOperationMessageResource
    {
        try {
            return StartTrainingModel::run($dataset, $problemDetail);
        } catch (ConnectionException $e) {
            throw new MlEngineConnectionException($e->getMessage());
        } catch (RequestException $e) {
            throw new MlEngineRequestException($e->response?->json('error') ?? $e->getMessage(), $e->response?->status());
        }
    }
}

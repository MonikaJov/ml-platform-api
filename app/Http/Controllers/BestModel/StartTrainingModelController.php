<?php

declare(strict_types=1);

namespace App\Http\Controllers\BestModel;

use App\Actions\BestModel\StartTrainingModel;
use App\Exceptions\MlEngine\MlEngineConnection;
use App\Exceptions\MlEngine\MlEngineRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\SuccessfulOperationMessageResource;
use App\Models\Dataset;
use App\Models\ProblemDetail;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

#[Group('Best Model')]
final class StartTrainingModelController extends Controller
{
    /**
     * api.dataset.problem-detail.best-models.train
     *
     * @throws MlEngineRequest|MlEngineConnection
     */
    public function __invoke(Dataset $dataset, ProblemDetail $problemDetail): SuccessfulOperationMessageResource
    {
        try {
            return StartTrainingModel::run($dataset, $problemDetail);
        } catch (ConnectionException $e) {
            throw new MlEngineConnection($e->getMessage());
        } catch (RequestException $e) {
            throw new MlEngineRequest($e->response?->json('error') ?? $e->getMessage(), $e->response?->status());
        }
    }
}

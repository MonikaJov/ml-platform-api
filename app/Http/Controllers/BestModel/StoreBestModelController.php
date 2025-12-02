<?php

declare(strict_types=1);

namespace App\Http\Controllers\BestModel;

use App\Actions\BestModel\StoreBestModel;
use App\Http\Controllers\Controller;
use App\Http\Requests\BestModel\StoreBestModelRequest;
use App\Http\Resources\BestModel\BestModelResource;
use Dedoc\Scramble\Attributes\Group;

#[Group('Best Model')]
final class StoreBestModelController extends Controller
{
    /**
     * api.best-models.store
     */
    public function __invoke(StoreBestModelRequest $request): BestModelResource
    {
        return StoreBestModel::run($request);
    }
}

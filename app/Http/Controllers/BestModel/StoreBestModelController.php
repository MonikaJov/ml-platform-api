<?php

namespace App\Http\Controllers\BestModel;

use App\Actions\BestModel\StoreBestModel;
use App\Http\Controllers\Controller;
use App\Http\Requests\BestModel\StoreBestModelRequest;
use App\Http\Resources\BestModel\BestModelResource;

class StoreBestModelController extends Controller
{
    /**
     * api.best-models.store
     */
    public function __invoke(StoreBestModelRequest $request): BestModelResource
    {
        return StoreBestModel::run($request);
    }
}

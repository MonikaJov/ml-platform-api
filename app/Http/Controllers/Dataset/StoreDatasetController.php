<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dataset;

use App\Actions\Dataset\StoreDataset;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dataset\StoreDatasetRequest;
use App\Http\Resources\Dataset\DatasetResource;

final class StoreDatasetController extends Controller
{
    /**
     * api.datasets.store
     */
    public function __invoke(StoreDatasetRequest $request): DatasetResource
    {
        return StoreDataset::run($request);
    }
}

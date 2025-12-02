<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dataset;

use App\Actions\Dataset\IndexDataset;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dataset\IndexDatasetRequest;
use App\Http\Resources\Dataset\DatasetResourceCollection;

final class IndexDatasetController extends Controller
{
    /**
     * api.datasets.index
     */
    public function __invoke(IndexDatasetRequest $request): DatasetResourceCollection
    {
        return IndexDataset::run($request);
    }
}

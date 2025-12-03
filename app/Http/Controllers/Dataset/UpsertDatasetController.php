<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dataset;

use App\Actions\Dataset\UpsertDataset;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dataset\UpsertDatasetRequest;
use App\Http\Resources\Dataset\DatasetResource;
use App\Models\Dataset;
use Dedoc\Scramble\Attributes\Group;

#[Group('Dataset')]
final class UpsertDatasetController extends Controller
{
    /**
     * api.datasets.upsert
     */
    public function __invoke(UpsertDatasetRequest $request, Dataset $dataset): DatasetResource
    {
        return UpsertDataset::run($request, $dataset);
    }
}

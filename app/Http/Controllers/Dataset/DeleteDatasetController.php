<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dataset;

use App\Actions\Dataset\DeleteDataset;
use App\Http\Controllers\Controller;
use App\Http\Resources\SuccessfulOperationMessageResource;
use App\Models\Dataset;
use Dedoc\Scramble\Attributes\Group;

#[Group('Dataset')]
final class DeleteDatasetController extends Controller
{
    /**
     * api.datasets.delete
     */
    public function __invoke(Dataset $dataset): SuccessfulOperationMessageResource
    {
        return DeleteDataset::run($dataset);
    }
}

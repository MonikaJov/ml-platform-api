<?php

declare(strict_types=1);

namespace App\Actions\Dataset;

use App\Http\Requests\Dataset\UpsertDatasetRequest;
use App\Http\Resources\Dataset\DatasetResource;
use App\Models\Dataset;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

final class UpsertDataset
{
    use AsAction;

    public function handle(UpsertDatasetRequest $request, Dataset $dataset): DatasetResource
    {
        $uploadedFile = $request->file('dataset');

        Storage::disk('datasets')->putFileAs(dirname($dataset->path), $uploadedFile, basename($dataset->path));

        $dataset->update(Arr::except($request->validated(), 'dataset'));

        return DatasetResource::make($dataset);
    }
}

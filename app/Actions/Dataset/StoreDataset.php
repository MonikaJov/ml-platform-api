<?php

namespace App\Actions\Dataset;

use App\Http\Requests\Dataset\StoreDatasetRequest;
use App\Http\Resources\Dataset\DatasetResource;
use App\Models\Dataset;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreDataset
{
    use AsAction;

    public function handle(StoreDatasetRequest $request): DatasetResource
    {
        $validated = $request->validated();
        $file = $validated['dataset'];
        $client = auth()->user();

        $filename = $client->username.'_'.time().'.'.$file->getClientOriginalExtension();

        $path = $file->storeAs("{$client->id}", $filename, 'datasets');

        $dataset = Dataset::create([
            'path' => $path,
            'user_id' => $client->id,
            'has_null' => $validated['has_null'],
        ]);

        return DatasetResource::make($dataset);
    }
}

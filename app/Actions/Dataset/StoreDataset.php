<?php

namespace App\Actions\Dataset;

use App\Http\Requests\Dataset\StoreDatasetRequest;
use App\Http\Resources\Dataset\DatasetResource;
use App\Models\Dataset;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreDataset
{
    use AsAction;

    public function handle(StoreDatasetRequest $request): DatasetResource
    {
        $path = self::storeFile($request->validated()['dataset'], auth()->user());

        $dataset = Dataset::create([
            'path' => $path,
            ...Arr::except($request->validated(), 'dataset'),
        ]);

        return DatasetResource::make($dataset);
    }

    private function storeFile(UploadedFile $file, User $client): string
    {
        $filename = $client->username.'_'.time().Str::uuid()->toString().'.'.$file->getClientOriginalExtension();

        return $file->storeAs("{$client->id}", $filename, 'datasets');
    }
}

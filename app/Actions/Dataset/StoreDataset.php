<?php

declare(strict_types=1);

namespace App\Actions\Dataset;

use App\Http\Requests\Dataset\StoreDatasetRequest;
use App\Http\Resources\Dataset\DatasetResource;
use App\Models\Dataset;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class StoreDataset
{
    use AsAction;

    public function handle(StoreDatasetRequest $request): DatasetResource
    {
        $dataset = Dataset::create([
            'path' => self::storeFile($request->validated()['dataset'], auth()->user()),
            'column_names' => self::getHeaders($request->validated()['dataset']),
            ...Arr::except($request->validated(), 'dataset'),
        ]);

        return DatasetResource::make($dataset);
    }

    private function storeFile(UploadedFile $file, User $client): string
    {
        $filename = $client->username.'_'.time().Str::uuid()->toString().'.'.$file->getClientOriginalExtension();

        return $file->storeAs("{$client->id}", $filename, 'datasets');
    }

    private function getHeaders(UploadedFile $file): string
    {
        $headers = [];

        $handle = fopen($file->getRealPath(), 'r');
        if ($handle !== false) {
            $line = fgetcsv($handle);
            $headers = $line !== false ? $line : [];
            fclose($handle);
        }

        return implode(',', $headers);
    }
}

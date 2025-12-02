<?php

declare(strict_types=1);

namespace App\Actions\Dataset;

use App\Filters\Dataset\DatasetNameFilter;
use App\Http\Requests\Dataset\IndexDatasetRequest;
use App\Http\Resources\Dataset\DatasetResourceCollection;
use App\Models\Dataset;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class IndexDataset
{
    use AsAction;

    public function handle(IndexDatasetRequest $request): DatasetResourceCollection
    {
        $datasets = QueryBuilder::for(Dataset::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::custom('name', new DatasetNameFilter()),
            ])
            ->allowedSorts(['id', 'created_at', 'updated_at'])
            ->defaultSort('-created_at')
            ->with([
                'problemDetail.bestModel',
            ])
            ->where('user_id', auth()->user()->id)
            ->paginate(
                perPage: $request->limit,
                page: $request->page
            );

        return DatasetResourceCollection::make($datasets);
    }
}

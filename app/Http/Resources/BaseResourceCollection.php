<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseResourceCollection extends ResourceCollection
{
    protected array $pagination = [];

    public function __construct(mixed $resource)
    {
        if ($resource instanceof LengthAwarePaginator) {
            $this->pagination = [
                'page' => $resource->currentPage(),
                'limit' => $resource->perPage(),
                'total_records' => $resource->total(),
                'total_pages' => $resource->lastPage(),
                'filter' => request()->input('filter') ?? '',
                'sort' => request()->input('sort') ?? '',
                'state' => request()->input('state') ?? '',
            ];
        }

        parent::__construct($resource);
    }

    public function withResponse(Request $request, JsonResponse $response): void
    {
        $data = $response->getData(true);
        unset($data['meta'], $data['links']);

        if (count($this->pagination)) {
            $data = array_merge($this->pagination, $data['data']);
        }

        $response->setData($data);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Resources\Dataset;

use App\Http\Resources\BaseResourceCollection;
use App\Models\Dataset;
use App\Traits\ResourceCollectionToArray;

/** @see Dataset */
final class DatasetResourceCollection extends BaseResourceCollection
{
    use ResourceCollectionToArray;
}

<?php

namespace App\Http\Resources\Dataset;

use App\Http\Resources\BaseResourceCollection;
use App\Models\Dataset;
use App\Traits\ResourceCollectionToArray;

/** @see Dataset */
class DatasetResourceCollection extends BaseResourceCollection
{
    use ResourceCollectionToArray;
}

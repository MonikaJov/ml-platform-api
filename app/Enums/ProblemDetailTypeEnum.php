<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum ProblemDetailTypeEnum: string
{
    use EnumToArray;

    case REGRESSION = 'regression';
    case CLASSIFICATION = 'classification';
    case BINARY_CLASSIFICATION = 'binary_classification';
    case CLUSTERING = 'clustering';
}

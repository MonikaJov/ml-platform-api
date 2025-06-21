<?php

namespace App\Models;

use App\Enums\ProblemDetailTypeEnum;
use Database\Factories\ProblemDetailFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property ProblemDetailTypeEnum $type
 * @property string $target_column
 * @property int $dataset_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ProblemDetail extends Model
{
    /** @use HasFactory<ProblemDetailFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'type' => ProblemDetailTypeEnum::class,
    ];

    /** @return BelongsTo<Dataset, covariant ProblemDetail> */
    public function dataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class);
    }

    protected static function newFactory(): ProblemDetailFactory
    {
        return ProblemDetailFactory::new();
    }
}

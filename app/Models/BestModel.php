<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\BestModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $path
 * @property int $problem_detail_id
 * @property string $name
 * @property string $performance
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class BestModel extends Model
{
    /** @use HasFactory<BestModelFactory> */
    use HasFactory;

    protected $guarded = [];

    /** @return BelongsTo<ProblemDetail, covariant BestModel> */
    public function problemDetail(): BelongsTo
    {
        return $this->belongsTo(ProblemDetail::class);
    }

    protected static function newFactory(): BestModelFactory
    {
        return BestModelFactory::new();
    }
}

<?php

namespace App\Models;

use Database\Factories\DatasetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Observed by DatasetObserver::class
 *
 * @property int $id
 * @property string $path
 * @property int $user_id
 * @property string $column_names
 * @property bool $has_null
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Dataset extends Model
{
    /** @use HasFactory<DatasetFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'has_null' => 'boolean',
    ];

    /** @return BelongsTo<User, covariant Dataset> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasOne<ProblemDetail, covariant Dataset> */
    public function problemDetail(): HasOne
    {
        return $this->hasOne(ProblemDetail::class);
    }

    protected static function newFactory(): DatasetFactory
    {
        return DatasetFactory::new();
    }
}

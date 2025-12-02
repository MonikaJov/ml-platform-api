<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\DatasetFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

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
 * @property string $full_path
 * @property string $name
 */
final class Dataset extends Model
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

    /** @return Attribute<int, null> */
    protected function fullPath(): Attribute
    {
        return new Attribute(
            get: fn () => (Storage::disk('datasets')->path($this->path))
        );
    }

    /** @return Attribute<int, null> */
    protected function name(): Attribute
    {
        return new Attribute(
            get: fn () => pathinfo($this->path, PATHINFO_FILENAME)
        );
    }
}

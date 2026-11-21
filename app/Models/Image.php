<?php

namespace App\Models;

use Database\Factories\ImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperImage
 */
#[Fillable(['disk', 'path', 'sort_order'])]
class Image extends Model implements Auditable
{
    /** @use HasFactory<ImageFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /** @return MorphTo<Model, $this> */
    public function imageable(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }
}

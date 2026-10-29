<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * One editable section of site content, keyed by its dot-path in
 * config/portfolio.php (e.g. "pages.home.hero" or "services").
 */
#[Fillable(['key', 'value'])]
class Content extends Model
{
    protected function casts(): array
    {
        return ['value' => 'array'];
    }
}

<?php

namespace App\Support;

use App\Models\Content;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Site content = the defaults in config/portfolio.php, with any sections the
 * admin has edited (rows in `contents`, keyed by dot-path) laid on top.
 *
 * Overrides are applied to the config repository once per request, so the
 * views and controllers keep reading config('portfolio.*') as before.
 */
class ContentRepository
{
    private const CACHE_KEY = 'site-content-overrides';

    /** Untouched defaults, captured before overrides are applied. */
    private array $defaults = [];

    public function apply(): void
    {
        $this->defaults = config('portfolio', []);

        // Broader keys first, so a nested section always wins over its parent.
        $overrides = $this->overrides();
        uksort($overrides, fn ($a, $b) => substr_count($a, '.') <=> substr_count($b, '.') ?: strcmp($a, $b));

        foreach ($overrides as $key => $value) {
            config(["portfolio.{$key}" => $value]);
        }
    }

    /**
     * @return array<string, mixed> key => stored value
     */
    public function overrides(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, fn () => Content::query()
                ->get(['key', 'value'])
                ->mapWithKeys(fn (Content $row) => [$row->key => $row->value])
                ->all());
        } catch (Throwable) {
            // No database yet (fresh install, migrations not run): defaults only.
            return [];
        }
    }

    public function get(string $key, mixed $fallback = null): mixed
    {
        return config("portfolio.{$key}", $fallback);
    }

    public function default(string $key): mixed
    {
        return data_get($this->defaults, $key);
    }

    public function save(string $key, mixed $value): void
    {
        Content::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(self::CACHE_KEY);
        config(["portfolio.{$key}" => $value]);
    }

    /** Drop the admin's version of a section and go back to the default. */
    public function reset(string $key): void
    {
        Content::query()->where('key', $key)->delete();
        Cache::forget(self::CACHE_KEY);
        config(["portfolio.{$key}" => $this->default($key)]);
    }

    public function isCustomized(string $key): bool
    {
        return array_key_exists($key, $this->overrides());
    }

    /**
     * When each of the given sections was last edited (missing = never).
     *
     * @param  array<int, string>  $keys
     * @return array<string, \Illuminate\Support\Carbon>
     */
    public function editedAt(array $keys): array
    {
        try {
            return Content::query()->whereIn('key', $keys)->get(['key', 'updated_at'])
                ->mapWithKeys(fn (Content $row) => [$row->key => $row->updated_at])
                ->all();
        } catch (Throwable) {
            return [];
        }
    }
}

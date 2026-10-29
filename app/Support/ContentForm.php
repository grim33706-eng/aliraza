<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

/**
 * Turns a submitted admin section form back into a content value, following
 * the section's schema (config/admin.php). Field values arrive under
 * data[...], uploads under the mirrored files[...] path.
 */
class ContentForm
{
    private const TEXT_MAX = 500;

    private const TEXTAREA_MAX = 5000;

    /** @var array<string, string> dot-path => message */
    private array $errors = [];

    /** @var array<int, string> uploads replaced during this save (deleted on success) */
    private array $replaced = [];

    /** @var array<string, array{0: int, 1: int}> dot-path => [width, height] of new image uploads */
    private array $dimensions = [];

    public function __construct(private MediaStore $media) {}

    /**
     * @param  array  $section  schema of one section
     * @param  mixed  $current  the section's current value (keeps keys the form doesn't edit)
     */
    public function fromRequest(array $section, Request $request, mixed $current): mixed
    {
        $this->errors = [];
        $this->replaced = [];
        $this->dimensions = [];
        $root = self::rootField($section);

        $value = $this->cast($root, $request->input('data'), 'data', $request);

        // A group section keeps any keys the schema doesn't cover.
        if ($root['type'] === 'group' && is_array($current)) {
            $value = array_merge($current, $value);
        }

        if ($this->errors) {
            throw ValidationException::withMessages($this->errors);
        }

        return $value;
    }

    /** Previous uploads that were replaced; delete only after a successful save. */
    public function replacedUploads(): array
    {
        return $this->replaced;
    }

    /** The section itself, described as a single field. */
    public static function rootField(array $section): array
    {
        return [
            'type' => $section['type'] ?? 'group',
            'fields' => $section['fields'] ?? [],
            'item_label' => $section['item_label'] ?? null,
            'label' => $section['title'],
        ];
    }

    private function cast(array $field, mixed $input, string $path, Request $request): mixed
    {
        return match ($field['type']) {
            'group' => $this->castGroup($field['fields'], is_array($input) ? $input : [], $path, $request),
            'repeater' => array_values(array_map(
                fn ($key) => $this->castGroup($field['fields'], (array) $input[$key], "{$path}.{$key}", $request),
                array_keys(is_array($input) ? $input : []),
            )),
            'list' => $this->castList($input, $field, $path),
            'numbers' => array_values(array_map(fn ($n) => $n + 0, array_filter((array) ($input ?? []), fn ($n) => is_numeric($n)))),
            'number' => $this->castNumber($input, $field, $path),
            'toggle' => filter_var($input, FILTER_VALIDATE_BOOL),
            'select', 'icon' => $this->castSelect($input, $field, $path),
            'image', 'video' => $this->castMedia($input, $field, $path, $request),
            'email' => $this->castEmail($input, $field, $path),
            'url' => $this->castUrl($input, $field, $path),
            'textarea', 'rich' => $this->castText($input, $field, $path, self::TEXTAREA_MAX),
            default => $this->castText($input, $field, $path, self::TEXT_MAX),
        };
    }

    private function castGroup(array $fields, array $input, string $path, Request $request): array
    {
        $out = [];
        foreach ($fields as $field) {
            $out[$field['name']] = $this->cast($field, $input[$field['name']] ?? null, "{$path}.{$field['name']}", $request);
        }

        // A freshly uploaded image fills in its sibling width/height fields.
        foreach ($fields as $field) {
            if (isset($field['dimensions'], $this->dimensions["{$path}.{$field['name']}"])) {
                [$out[$field['dimensions'][0]], $out[$field['dimensions'][1]]] = $this->dimensions["{$path}.{$field['name']}"];
            }
        }

        return $out;
    }

    private function castText(mixed $input, array $field, string $path, int $max): string
    {
        $text = trim((string) (is_scalar($input) ? $input : ''));
        if (mb_strlen($text) > $max) {
            $this->errors[$path] = "{$field['label']} must be {$max} characters or fewer.";
        }

        return $text;
    }

    private function castList(mixed $input, array $field, string $path): array
    {
        $items = array_values(array_filter(array_map(
            fn ($item) => trim((string) (is_scalar($item) ? $item : '')),
            is_array($input) ? $input : [],
        ), fn ($item) => $item !== ''));

        foreach ($items as $item) {
            if (mb_strlen($item) > self::TEXTAREA_MAX) {
                $this->errors[$path] = "Each item in {$field['label']} must be ".self::TEXTAREA_MAX.' characters or fewer.';
            }
        }

        return $items;
    }

    private function castNumber(mixed $input, array $field, string $path): int|float|null
    {
        if ($input === null || $input === '') {
            return ! empty($field['integer']) ? 0 : null;
        }
        if (! is_numeric($input)) {
            $this->errors[$path] = "{$field['label']} must be a number.";

            return null;
        }

        return ! empty($field['integer']) ? (int) round((float) $input) : $input + 0;
    }

    private function castSelect(mixed $input, array $field, string $path): string
    {
        $value = (string) ($input ?? '');
        $options = array_keys(self::options($field));
        if ($options && ! in_array($value, $options, true)) {
            $this->errors[$path] = "Choose a valid option for {$field['label']}.";
        }

        return $value;
    }

    private function castEmail(mixed $input, array $field, string $path): string
    {
        $email = $this->castText($input, $field, $path, 254);
        if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$path] = "{$field['label']} must be a valid email address.";
        }

        return $email;
    }

    private function castUrl(mixed $input, array $field, string $path): string
    {
        $url = $this->castText($input, $field, $path, 2048);
        if ($url !== '' && ! preg_match('#^https?://#i', $url)) {
            $this->errors[$path] = "{$field['label']} must start with http:// or https://.";
        }

        return $url;
    }

    private function castMedia(mixed $input, array $field, string $path, Request $request): string
    {
        // Current path from the hidden input; only bundled or uploaded files are allowed.
        $current = trim((string) (is_scalar($input) ? $input : ''));
        if ($current !== '' && ! preg_match('#^(images|videos|storage/uploads)/[\w./-]+$#', $current)) {
            $current = '';
        }

        $file = $request->file('files.'.Str::after($path, 'data.'));
        if (! $file instanceof UploadedFile) {
            if ($current === '' && empty($field['optional'])) {
                $this->errors[$path] = "{$field['label']} is required.";
            }

            return $current;
        }

        if (! $file->isValid()) {
            $this->errors[$path] = "{$field['label']} failed to upload (it may be larger than the server allows).";

            return $current;
        }

        try {
            if ($field['type'] === 'video') {
                $stored = $this->media->storeVideo($file);
            } else {
                $image = $this->media->storeImage($file);
                $stored = $image['path'];
                $this->dimensions[$path] = [$image['width'], $image['height']];
            }
        } catch (RuntimeException $e) {
            $this->errors[$path] = $e->getMessage();

            return $current;
        }

        if ($current !== '') {
            $this->replaced[] = $current;
        }

        return $stored;
    }

    /**
     * Options for a select/icon field: static, or pulled from current content
     * via "options_from" (e.g. "service_groups.*.name").
     *
     * @return array<string, string>
     */
    public static function options(array $field): array
    {
        if ($field['type'] === 'icon') {
            return config('admin.icons');
        }
        if (isset($field['options_from'])) {
            $values = array_filter((array) data_get(config('portfolio'), $field['options_from']), 'is_scalar');

            return array_combine(array_map('strval', $values), array_map('strval', $values)) ?: [];
        }

        return $field['options'] ?? [];
    }
}

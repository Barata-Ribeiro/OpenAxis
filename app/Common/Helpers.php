<?php

namespace App\Common;

class Helpers
{
    /**
     * Build a boolean-mode search query from a raw input string.
     *
     * This method parses and normalizes a user-provided search string and returns
     * a sanitized boolean-style query suitable for use in full-text search engines
     * (for example, MySQL's MATCH ... AGAINST (... IN BOOLEAN MODE)).
     *
     * The implementation is expected to:
     *  - Trim and normalize whitespace,
     *  - Preserve quoted phrases as single units,
     *  - Apply or normalize boolean operators/flags for individual terms,
     *  - Escape or remove characters that would break the target query syntax.
     *
     * @param  string  $query  Raw user-provided search string.
     * @return string A sanitized, normalized boolean-mode query string ready for use in full-text searches.
     */
    public static function buildBooleanQuery(string $query): string
    {
        $terms = preg_split('/\s+/', $query, -1, PREG_SPLIT_NO_EMPTY);

        $booleanParts = array_map(function ($t) {
            $t = preg_replace('/[+\-<>()~\"*]/', '', $t);

            return $t !== '' ? '+'.$t.'*' : null;
        }, $terms);

        $booleanParts = array_filter($booleanParts);

        return $booleanParts ? implode(' ', $booleanParts) : $query;
    }

    /**
     * Normalize a filter specification into an indexed array of associative filters
     * using the given key.
     *
     * This helper accepts flexible input forms and returns a consistent structure
     * suitable for building queries or further processing:
     * - If $filters is a CSV string (comma-separated), it will be split into values
     *   and each trimmed.
     * - If $filters is an indexed array of scalar values, each element will be
     *   converted to an associative array using $key.
     * - If $filters is already an array of associative arrays that include $key,
     *   those entries are preserved.
     * - Empty values are ignored and result in an empty array.
     *
     * Examples:
     * - buildFiltersArray('a,b,c', 'tag') =>
     *     [['tag' => 'a'], ['tag' => 'b'], ['tag' => 'c']]
     * - buildFiltersArray(['active','pending'], 'status') =>
     *     [['status' => 'active'], ['status' => 'pending']]
     * - buildFiltersArray([['status' => 'active']], 'status') => unchanged
     *
     * @param  string|array  $filters  Filters as a CSV string, indexed array of scalars,
     *                                 or an array of associative filters.
     * @param  string  $key  The key to apply to each filter value in the resulting arrays.
     * @return array<int, array<string, mixed>> An indexed array of associative filters
     *                                          each mapping $key to a value.
     *
     * @throws \InvalidArgumentException If provided filters cannot be normalized.
     */
    public static function buildFiltersArray(string|array $filters, string $key): array
    {
        $values = [];

        if (is_array($filters)) {
            // Direct key
            if (isset($filters[$key])) {
                $values = is_array($filters[$key])
                    ? $filters[$key]
                    : array_filter(array_map('trim', explode(',', (string) $filters[$key])));
            } else {
                // Look for values like "key:val1,val2" among indexed items
                foreach ($filters as $f) {
                    if (! is_string($f)) {
                        continue;
                    }

                    if (str_starts_with($f, $key.':')) {
                        $values = array_merge($values, array_filter(array_map('trim', explode(',', substr($f, strlen($key) + 1)))));
                    }
                }
            }
        } elseif (is_string($filters) && $filters !== '') {
            // Format: "key1:val1,key2:val2" -> find the key segment
            $values = array_filter(array_map('trim', explode(',', explode(':', $filters, 2)[1] ?? '')));
        }

        // Normalize and reindex
        return array_values(array_filter((array) $values));
    }
}

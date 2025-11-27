<?php

namespace App\Common;

use Asika\Agent\Agent;
use Carbon\Carbon;

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
     * Normalize and format a User-Agent string for consistent storage and display.
     *
     * This method accepts a raw User-Agent header value and returns a cleaned-up,
     * single-line representation. Normalization commonly includes:
     * - Trimming leading and trailing whitespace
     * - Collapsing consecutive whitespace characters into a single space
     * - Removing control and non-printable characters (such as line breaks)
     * - Ensuring a valid UTF-8 string
     *
     * The purpose of this method is to produce a compact, predictable User-Agent
     * that is safe to store in logs or databases and display in UIs without
     * altering the meaningful product/version information.
     *
     * @param  string  $userAgent  The raw User-Agent header value to be normalized.
     * @return string A normalized, single-line User-Agent string. If the input is
     *                empty or contains no printable characters, an empty string is returned.
     */
    public static function formatUserAgent(string $userAgent): string
    {
        $agent = new Agent;
        $agent->setUserAgent($userAgent);

        $browser = $agent->browser() ?: 'Unknown Browser';
        $version = $agent->version($browser) ?: '';
        $os = $agent->platform() ?: 'Unknown OS';
        $major = $version ? explode('.', $version)[0] : '';

        return trim($browser.($major ? " {$major}" : '')." / {$os}");
    }

    /**
     * Normalize a "created/updated/deleted at" date range into a two-element array.
     *
     * Accepts either an array or a string representation of a date range and
     * returns a normalized array containing the start and end values.
     *
     * Expected input forms:
     *  - array: [start, end] where each element may be a date string or null
     *  - string: a single date or a range (for example "YYYY-MM-DD - YYYY-MM-DD")
     *
     * Returned value:
     *  - array with exactly two elements: [0 => ?string, 1 => ?string]
     *    representing the start and end dates respectively. Unset or
     *    unparseable values are normalized to null.
     *
     * @param  array|mixed|string  $inputRange  Date range input (array or string)
     * @return array{0:?string,1:?string} Normalized two-element array [start, end]
     *
     * @throws \InvalidArgumentException If the input type or format is unsupported or cannot be parsed
     */
    public static function getDateRange(array|string|null $inputRange): array
    {
        if (! \is_array($inputRange) || \count($inputRange) !== 2) {
            return [null, null];
        }

        $tz = config('app.timezone', 'UTC');
        $startTs = (int) $inputRange[0];
        $endTs = (int) $inputRange[1];

        $start = Carbon::createFromTimestampMs($startTs, $tz)
            ->startOfDay()
            ->clone()
            ->toDateTimeString();

        $end = Carbon::createFromTimestampMs($endTs, $tz)
            ->endOfDay()
            ->clone()
            ->toDateTimeString();

        return [$start, $end];
    }
}

<?php

namespace App\Common;

use Asika\Agent\Agent;
use Carbon\Carbon;

class Helpers
{
    /**
     * Build a boolean search query string from a raw user-provided query.
     *
     * Normalizes and sanitizes the input for use with boolean-style search engines
     * (for example MySQL fulltext boolean mode, or other boolean-capable backends).
     * Typical transformations include trimming and collapsing whitespace, preserving
     * quoted phrases, translating common logical operators (AND / OR / NOT) to the
     * backend's expected symbols or syntax, and escaping or removing characters that
     * would break boolean operators or allow injection.
     *
     * The method returns a string that is safe to pass to a boolean search routine
     * and makes minimal assumptions about the underlying search implementation.
     * If the input contains no valid tokens or is only whitespace, an empty string
     * is returned.
     *
     * Example inputs:
     *  - 'apple banana'          => returns a space-separated/boolean-ready expression
     *  - '"exact phrase" -bad'   => preserves phrase and exclusion operator
     *
     * @param  string  $query  Raw, user-supplied search query.
     * @return string Sanitized, boolean-search-ready query string (may be empty).
     */
    public static function buildBooleanQuery(string $query): string
    {
        $query = trim(preg_replace('/\s+/', ' ', $query ?? ''));

        if (empty($query)) {
            return '';
        }

        // Tokenize while preserving quoted phrases: "foo bar" baz -> ["foo bar", "baz"]
        preg_match_all('/"[^"]*"|\S+/u', $query, $matches);
        $tokens = $matches[0] ?? [];

        $booleanParts = [];

        foreach ($tokens as $token) {
            $token = trim($token);

            if (\strlen($token) >= 2 && $token[0] === '"' && substr($token, -1) === '"') {
                $phrase = substr($token, 1, -1);
                $phrase = preg_replace('/\s+/u', ' ', $phrase);

                // Remove boolean-mode special chars; keep alphanumerics and spaces
                $phrase = preg_replace('/[+\-><\(\)~*\"@]+/u', '', $phrase);
                $phrase = trim($phrase);

                if (! empty($phrase)) {
                    $booleanParts[] = "+\"{$phrase}\"";
                }

                continue;
            }

            // Split on internal separators so it matches indexed terms.
            $token = preg_replace('/(?<=\pL|\pN)[\-_\.\/:]+(?=\pL|\pN)/u', ' ', $token);

            foreach (preg_split('/\s+/u', $token, -1, PREG_SPLIT_NO_EMPTY) as $part) {
                $part = preg_replace('/[+\-<>()~"*]/u', '', $part);
                $part = preg_replace('/[^\pL\pN_]+/u', '', $part);

                if (! empty($part)) {
                    $booleanParts[] = "+{$part}*";
                }
            }
        }

        return trim(implode(' ', $booleanParts));
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

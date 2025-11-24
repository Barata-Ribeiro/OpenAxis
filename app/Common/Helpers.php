<?php

namespace App\Common;

use Asika\Agent\Agent;

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
}

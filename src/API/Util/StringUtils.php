<?php

namespace GroupByInc\API\Util;

class StringUtils
{
    public static function startsWith($string, $subString) {
        return strpos($string, $subString) === 0;
    }

    public static function endsWith($string, $subString) {
        return substr($string, -strlen($subString)) === $subString;
    }


    /**
     * Checks if a `string` is whitespace, empty (`''`) or `null`.
     *
     *     StringUtils::isBlank(null);      // true
     *     StringUtils::isBlank('');        // true
     *     StringUtils::isBlank(' ');       // true
     *     StringUtils::isBlank('bob');     // false
     *     StringUtils::isBlank('  bob  '); // false
     *
     * @param string $str The `string` to check.
     *
     * @return boolean `true` if the `string` is `null`, empty or whitespace;
     *    `false` otherwise.
     */
    public static function isBlank($str)
    {
        $trimmed = trim($str);
        return empty($trimmed);
    }
    /**
     * Checks if a `string` is not empty (`''`), not `null` and not whitespace
     * only.
     *
     *     StringUtils::isNotBlank(null);      // false
     *     StringUtils::isNotBlank('');        // false
     *     StringUtils::isNotBlank(' ');       // false
     *     StringUtils::isNotBlank('bob');     // true
     *     StringUtils::isNotBlank('  bob  '); // true
     *
     * @param string $str The `string` to check.
     *
     * @return boolean `true` if the `string` is not empty and not `null` and
     *    not whitespace; `false` otherwise.
     */
    public static function isNotBlank($str)
    {
        return !self::isBlank($str);
    }
}
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
}
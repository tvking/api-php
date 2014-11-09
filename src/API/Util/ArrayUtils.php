<?php

namespace GroupByInc\API\Util;

class ArrayUtils
{
    /**
     * @param mixed[] $arr
     * @param mixed   $element
     */
    public static function remove(array &$arr, $element)
    {
        $index = array_search($element, $arr);
        self::removeByIndex($arr, $index);
    }

    /**
     * @param mixed[] $arr
     * @param int      $index
     * @return object
     */
    public static function removeByIndex(array &$arr, $index)
    {
        $element = $arr[$index];
        unset($arr[$index]);
        $arr = array_values($arr);
        return $element;
    }
}
<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/bluzman
 */

namespace Bluzman\Helper;

/**
 * ArrayHelper
 *
 * @author Pavel Machekhin
 * @created 2014-06-18 18:01
 */
class ArrayHelper
{
    /**
     * Get an item from an array using "dot" notation.
     *
     * Thanks to Illuminate. https://laravel.com/docs/5.4/helpers#method-array-get
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }
        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) or !array_key_exists($segment, $array)) {
                return $default instanceof \Closure ? $default() : $default;
            }
            $array = $array[$segment];
        }

        return $array;
    }
}

<?php
/**
 * @created 18/06/14 6:01PM
 * @author Pavel Machekhin <pavel.machekhin@gmail.com>
 */

namespace Bluzman\Helper;

class ArrayHelper
{
    /**
     * Get an item from an array using "dot" notation.
     *
     * Thanks to Illuminate. http://laravel.com/api/source-function-array_get.html#226-251
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    static public function get($array, $key, $default = null)
    {
        if (is_null($key)) return $array;
        if (isset($array[$key])) return $array[$key];

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) or !array_key_exists($segment, $array)) {
                return $default instanceof \Closure ? $default() : $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }

}
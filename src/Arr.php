<?php namespace _20TRIES\Arr;

/**
 * Helper class for interacting with arrays in a simple and clean way.
 *
 * @package _20TRIES\Arr
 */
class Arr
{
    /**
     * Gets a parameter from a request.
     *
     * @param array $arr
     * @param string $key
     * @param mixed $default
     * @return null
     */
    public static function get($arr, $key, $default = null)
    {
        self::has($arr, $key, $default);
        return $default;
    }

    /**
     * Determines whether an array has a given set of keys.
     *
     * @param array $arr
     * @param string|int|array $keys
     * @param mixed $value The value of the given key if only a single key is provided.
     * @return bool
     */
    public static function has($arr, $keys, &$value = null)
    {
        if (is_array($keys) && count($keys) > 1) {
            // Check multiple keys are all present
            return empty(array_diff(is_array($keys) ? $keys : [$keys], self::keys($arr)));
        } elseif(is_array($keys) && empty($arr)) {
            // Only possible true result would be if $keys is and array and is empty.
            return is_array($keys) && empty($keys);
        } elseif(is_array($keys)) {
            // If $keys only has one element, we will optimise it down to a string so that we can do a single key
            // lookup.
            $keys = self::first($keys);
        }
        foreach (self::splitKey($keys) as $key) {
            if (is_array($arr) && array_key_exists($key, $arr)) {
                $arr = $arr[$key];
            } else {
                return false;
            }
        }
        $value = $arr;
        return true;
    }

    /**
     * Gets the keys available in an array.
     *
     * @param array $arr
     * @return array
     */
    public static function keys($arr)
    {
        $output = [];
        self::traverse($arr, function ($item, $key) use (&$output) {
            $output[] = $key;
        });
        return $output;
    }

    /**
     * Traverses each item of a multidimensional array.
     *
     * @param array $arr
     * @param callable $callback
     */
    public static function traverse($arr, $callback)
    {
        if (empty($arr)) {
            return;
        }

        $lifo_queue = [];

        // The order of items is reversed because they will be processed in reverse when pop'ed off of the
        // queue.
        array_push($lifo_queue, ...array_reverse(array_map(function ($value, $key) {
            return ['key' => $key, 'value' => $value];
        }, $arr, array_keys($arr))));

        do {
            $item = array_pop($lifo_queue);

            // If the item value is an array and is not empty, we will push each of the items in that array back onto
            // the queue so that they will be processed next.
            if (is_array($item['value']) && !empty($item['value'])) {

                // The order of items is reversed because they will be processed in reverse when pop'ed off of the
                // queue.
                array_push($lifo_queue, ...array_reverse(array_map(function ($value, $key) use ($item) {
                    return [
                        'key'   => "{$item['key']}.{$key}",
                        'value' => $value,
                    ];
                }, $item['value'], array_keys($item['value']))));
            }

            // Pass the item to the callback function provided.
            $callback($item['value'], $item['key']);
        } while (! empty($lifo_queue));
    }

    /**
     * Splits a key into its components for "dot notation".
     *
     * @param string|int $key
     * @return array
     */
    protected static function splitKey($key)
    {
        return is_string($key) ? explode('.', $key) : [$key];
    }

    /**
     * Gets the first element from an array.
     *
     * @param array $arr
     * @param null|callable $callback An optional callback that can be provided that must return true before an element
     * is returned; takes the value and then the key of each element in the array.
     * @return mixed
     */
    public static function first($arr, $callback = null)
    {
        $result = null;
        foreach ($arr as $key => $item) {
            if (is_null($callback) || $callback($item, $key) === true) {
                $result = $item;
                break;
            }
        }
        return $result;
    }

    /**
     * Gets an array minus the first element.
     *
     * @param array $arr
     * @return array
     */
    public static function tail($arr)
    {
        return array_slice($arr, 1);
    }
}
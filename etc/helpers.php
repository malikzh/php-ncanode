<?php

if (! function_exists('iterator')) {
    function iterator($arr, $closure, $is_assoc = false): array
    {
        $result = [];
        foreach ($arr as $item) {
            if ($is_assoc) {
                $result[] = call_user_func_array($closure, $item);
            } else {
                $result[] = call_user_func($closure, $item);
            }
        }
        return $result;
    }
}

<?php

namespace Plainmotif\Mizzenlite\Helpers;

class StringHelper
{
    public static function toCamelCase($str, $replace, $capFirst = false)
    {
        if ($capFirst) {
            $str[0] = strtoupper($str[0]);
        }

        $func = function($c) { return strtoupper($c[1]); };

        return preg_replace_callback('/'.preg_quote($replace).'([a-zA-Z])/', $func, $str);
    }
}
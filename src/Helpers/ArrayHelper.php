<?php

namespace Plainmotif\Mizzenlite\Helpers;

class ArrayHelper
{
    /**
     * @todo this is imperfect, add disallowed var names handling (digit, dash, etc)
     */
    public static function configArrayToObject($config)
    {
        if (is_array($config)) {
            return (object) array_map(
                array(
                    'Plainmotif\Mizzenlite\helpers\ArrayHelper',
                    'configArrayToObject'
                ),
                $config
            );
        } else {
            return $config;
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: dtome
 * Date: 6/05/17
 * Time: 11:40
 */

namespace local_gradebook;

class Conditional
{
    const GREATER_THAN = 1;
    const LESS_THAN = 2;
    const GREATER_OR_EQUALS_THAN = 3;
    const LESS_OR_EQUALS_THAN = 4;
    const EQUALS_THAN = 5;

    public static function inArray()
    {
        return [
//            self::GREATER_THAN => '>',
//            self::LESS_THAN => '<',
            self::GREATER_OR_EQUALS_THAN => '>=',
            self::LESS_OR_EQUALS_THAN => '<=',
//            self::EQUALS_THAN => '='
        ];
    }
}

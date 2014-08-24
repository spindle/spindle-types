<?php
/**
 * spindle/types
 *
 * @license CC0-1.0 (Public Domain) https://creativecommons.org/publicdomain/zero/1.0/
 */
namespace Spindle\Types;

abstract class Enum
{
    private $scalar;
    private static $cache = array();

    function __construct($value)
    {
        $ref = new \ReflectionObject($this);
        $consts = $ref->getConstants();
        if (! in_array($value, $consts, true)) {
            throw new \InvalidArgumentException(
                'value must be ' . implode(',', $consts) . ". but $value passed."
            );
        }
        $this->scalar = $value;
    }

    final static function __callStatic($name, $args)
    {
        $class = get_called_class();
        $constantName = "$class::$name";
        if (isset(self::$cache[$constantName])) {
            return self::$cache[$constantName];
        } else {
            $const = constant($constantName);
            return self::$cache[$constantName] = new $class($const);
        }
    }

    final function valueOf()
    {
        return $this->scalar;
    }

    final function __toString()
    {
        return (string) $this->scalar;
    }
}

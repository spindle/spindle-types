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
        $const = constant("$class::$name");
        return new $class($const);
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

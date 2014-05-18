<?php
/**
 * spindle/types
 *
 * @license CC0-1.0 (Public Domain) https://creativecommons.org/publicdomain/zero/1.0/
 */
namespace Spindle\Types;

/**
 * TypedObjectを固定化するDecoratorクラス
 *
 */
class ConstObject implements
    \IteratorAggregate,
    \Countable
{
    protected $_object;

    final function __construct(TypedObject $obj)
    {
        $this->_object = $obj;
    }

    final function __get($name)
    {
        $val = $this->_object->$name;
        if ($val instanceof TypedObject) {
            return new self($val);
        } else {
            return $val;
        }
    }

    final function __set($name, $value)
    {
        throw new \DomainException(__CLASS__ . "->$name = $value is not allowed.");
    }

    function getIterator()
    {
        return $this->_object->getIterator();
    }

    function count()
    {
        return $this->_object->count();
    }
}

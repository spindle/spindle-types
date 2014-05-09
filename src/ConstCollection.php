<?php
/**
 * spindle/types
 *
 * @license CC0-1.0 (Public Domain) https://creativecommons.org/publicdomain/zero/1.0/
 */
namespace Spindle\Types;

/**
 * Collection型を書き換え不能にデコレートします。
 * ただし、内包するCollection自体は書き換え可能のままなので、
 * ConstCollectionを経由せずに編集することは可能です。
 */
class ConstCollection implements
    \ArrayAccess,
    \Countable,
    \IteratorAggregate
{
    private $collection;

    function __construct(Collection $c)
    {
        $this->collection = $c;
    }

    /**
     * 内部のCollectionのデータを読み出します
     * @param int $offset
     * @return mixed
     */
    function offsetGet($offset)
    {
        return $this->collection[$offset];
    }

    /**
     * 書き換えは禁止されています
     */
    final function offsetSet($offset, $value)
    {
        throw new \RuntimeException('ConstCollection is frozen.');
    }

    /**
     * 書き換えは禁止されています
     */
    final function offsetUnset($offset)
    {
        throw new \RuntimeException('ConstCollection is frozen.');
    }

    /**
     * 要素が実在するかを調べます
     * @param int $offset
     * @return bool
     */
    function offsetExists($offset)
    {
        return $this->collection->offsetExists($offset);
    }

    /**
     * @return \ArrayIterator
     */
    function getIterator()
    {
        return new \ArrayIterator((array)$this->collection);
    }

    /**
     * @return int
     */
    function count()
    {
        return count($this->collection);
    }
}

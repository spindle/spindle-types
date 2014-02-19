<?php
/**
 * Spindle\Types\Collection
 * 厳密な配列
 *
 * @license MIT
 */
namespace Spindle\Types;

abstract class Collection extends \SplFixedArray
{
    /**
     * 自動伸長させる
     * @param int $offset
     * @param mixed $value
     */
    function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $cnt = count($this);
            $this->setSize($cnt + 1);
            $offset = $cnt;
        }

        return parent::offsetSet($offset, $value);
    }

    function __set($name, $value)
    {
        throw new \RuntimeException('__set() is not allowed');
    }

    function __get($name)
    {
        throw new \RuntimeException('__get() is not allowed');
    }

    static function fromArray(array $array, $save_indexes=true)
    {
        $splFixedArray = \SplFixedArray::fromArray($array, $save_indexes);
        $cnt = count($array);
        $self = new static($cnt);
        foreach ($splFixedArray as $i => $v) {
            $self[$i] = $v;
        }

        return $self;
    }
}

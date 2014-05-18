<?php
/**
 * spindle/types
 *
 * @license CC0-1.0 (Public Domain) https://creativecommons.org/publicdomain/zero/1.0/
 */
namespace Spindle\Types;

/**
 * 厳密な配列を実現するクラス。
 * 添え字は0から始まり、必ず順番が守られる。
 * 文字列の添え字は許可されない。
 */
abstract class Collection extends \SplFixedArray
{
    /**
     * SplFixedArrayは固定長になってしまうため、自動伸長機能を追加
     * @param int $offset
     * @param mixed $value
     * @return void
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

    /**
     * SplFixedArray固定ではなく、継承した子クラスを返すように拡張
     *
     * @param array $array 元にする配列
     * @param bool $save_indexes なるべく元の添え字を保持しようとするかどうか
     * @return static
     */
    static function fromArray($array, $save_indexes=true)
    {
        $splFixedArray = \SplFixedArray::fromArray($array, $save_indexes);
        $cnt = count($array);
        $self = new static($cnt);
        foreach ($splFixedArray as $i => $v) {
            $self[$i] = $v;
        }

        return $self;
    }

    /**
     * getterは許可しない
     */
    function __get($name)
    {
        throw new \RuntimeException(__CLASS__ . "->$name is not allowed");
    }

    /**
     * setterは許可しない
     */
    function __set($name, $value)
    {
        throw new \RuntimeException(__CLASS__ . "->$name = $value is not allowed");
    }
}

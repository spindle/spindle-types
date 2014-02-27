<?php
namespace Spindle\Types\Tests;

use Spindle\Types;

class IntList extends Types\Collection
{
    function offsetSet($offset, $value)
    {
        if (! is_int($value)) {
            throw new \InvalidArgumentException("$value must be string.");
        }
        return parent::offsetSet($offset, $value);
    }
}

class ConstCollectionTest extends TestCase
{
    private $list;

    function setup()
    {
        $this->list = IntList::fromArray(array(10, 20));
    }

    /**
     * @test
     */
    function ConstCollectionの生成()
    {
        $clist = new Types\ConstCollection($this->list);

        self::assertCount(2, $clist);
        self::assertArrayHasKey(0, $clist);
        self::assertArrayHasKey(1, $clist);

        self::assertSame(10, $clist[0]);
        self::assertSame(20, $clist[1]);

        $copied = array();
        foreach ($clist as $i => $v) {
            $copied[$i] = $v;
        }

        self::assertEquals(array(10,20), $copied);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    function ConstCollectionは上書きできない()
    {
        $clist = new Types\ConstCollection($this->list);

        $clist[0] = 5;
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    function ConstCollectionは値を削除できない()
    {
        $clist = new Types\ConstCollection($this->list);

        unset($clist[1]);
    }
}

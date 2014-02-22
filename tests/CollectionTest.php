<?php
namespace Spindle\Types\Tests;

use Spindle\Types;

class StringList extends Types\Collection
{
    function offsetSet($offset, $value)
    {
        if (! is_string($value)) {
            throw new \InvalidArgumentException("$value must be string.");
        }
        return parent::offsetSet($offset, $value);
    }
}

class CollectionTest extends TestCase
{
    /**
     * @test
     */
    function 自動伸長します()
    {
        $list = new StringList;
        $list[] = 'a';
        $list[] = 'b';
        $list[] = 'c';

        self::assertCount(3, $list);
    }

    /**
     * fromArray
     * @test
     */
    function fromArrayのstatic化()
    {
        $list = StringList::fromArray(explode(',', 'a,b,c,d'));
        self::assertInstanceOf(__NAMESPACE__ . '\\StringList', $list);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    function set不可()
    {
        $list = new StringList;
        $list->foo = 123;
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    function get不可()
    {
        $list = new StringList;
        $foo = $list->foo;
    }
}

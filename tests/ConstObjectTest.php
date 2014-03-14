<?php
/**
 *
 */
namespace Spindle\Types\Tests;

use Spindle\Types;

class TypedObjectA extends Types\TypedObject
{
    static function schema()
    {
        return array(
            'int' => self::INT,
            'obj' => __CLASS__, null,
        );
    }

    function checkErrors()
    {
        return array();
    }
}

class ConstObjectTest extends TestCase
{
    /**
     * @test
     */
    function constの生成()
    {
        $typed = new TypedObjectA;
        $typed->int = 5;
        $typed->obj = new TypedObjectA;

        $const = new Types\ConstObject($typed);
        self::assertSame(5, $const->int);
        self::assertInstanceOf('Spindle\Types\ConstObject', $const->obj);
    }

    /**
     * @test
     * @expectedException \DomainException
     */
    function set不可()
    {
        $typed = new TypedObjectA;

        $const = new TYpes\ConstObject($typed);
        $const->int = 100;
    }

    /**
     * @test
     */
    function constObjectはforeach可能()
    {
        $typed = new TypedObjectA;

        $const = new Types\ConstObject($typed);
        foreach ($const as $i => $v) {
            self::assertNull($v);
        }

        self::assertCount(2, $const);
    }
}

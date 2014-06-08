<?php
namespace Spindle\Types\Tests;

use Spindle\Types;

//-----setup
class SomeModel extends Types\TypedObject
{
    private static $mode = 0;

    static function schema()
    {
        return array(
            'propInt' => self::INT, 8,
            'propDbl' => self::DBL, 8.8,
            'propStr' => self::STR, 'string',
            'propBoo' => self::BOOL, true,
            'propRes' => self::RES, fopen('php://input', 'r'),
            'propArr' => self::ARR, array(1,2,3),
            'propDat' => 'DateTime', new \DateTime,
            'propCal' => self::CALL, 'htmlspecialchars',
            'propMix' => self::MIX,
        );
    }

    function checkErrors()
    {
        return array();
    }

    function initialize()
    {
        $this->propMix = new \DateTime;
    }
}

class ChildModel extends SomeModel
{
    static function schema()
    {
        return self::extend(parent::schema(), array(
            'propRes' => fopen('php://input', 'r'),
            'propAdd' => self::BOOL, true,
        ));
    }
}

class TypedObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function SomeModelの初期化() {
        $model = new SomeModel;
        self::assertInstanceOf(__NAMESPACE__ . '\SomeModel', $model);
        self::assertInstanceOf('Iterator', $model->getIterator());
        self::assertSame(8, $model->propInt);
        self::assertSame(8.8, $model->propDbl);
        self::assertSame('string', $model->propStr);
        self::assertSame(true, $model->propBoo);
        self::assertInstanceOf('DateTime', $model->propMix);
        self::assertCount(9, $model);
    }

    /**
     * @test
     */
    function propSet正常系() {
        $model = new SomeModel;
        $model->propInt = 9;
        self::assertSame(9, $model->propInt);

        $model->propCal = 'htmlspecialchars';
        self::assertSame('htmlspecialchars', $model->propCal);

        $model->propMix = 'hogehoge';
        self::assertSame('hogehoge', $model->propMix);

        $model->propDat = new \DateTime;
        self::assertInstanceOf('DateTime', $model->propDat);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function propSet_型の違う値を渡した場合() {
        $model = new SomeModel;
        $model->propInt = '9';
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function propSet_Callableでない値を渡した場合() {
        $model = new SomeModel;
        $model->propCal = true;
    }

    /**
     * @test
     * @expectedException \OutOfRangeException
     */
    function propSet_定義していないプロパティにセット() {
        $model = new SomeModel;
        $model->uso800 = '9';
    }

    /**
     * @test
     * @expectedException \OutOfRangeException
     */
    function 存在しないプロパティを参照しようとするとエラー() {
        $model = new SomeModel;
        $v = $model->uso800;
    }

    /**
     * @test
     */
    function propSet_拡張を許可している場合() {
        Types\TypedObject::$preventExtensions = false;
        $model = new SomeModel;
        $model->uso800 = 123;
        self::assertSame(123, $model->uso800);
        Types\TypedObject::$preventExtensions = true;
    }

    /**
     * @test
     */
    function propGet_拡張を許可している場合() {
        Types\TypedObject::$preventExtensions = false;
        $model = new SomeModel;
        self::assertNull($model->uso800);
        Types\TypedObject::$preventExtensions = true;
    }

    /**
     * @test
     */
    function propGetキャストを許可している場合() {
        Types\TypedObject::$casting = true;
        $model = new SomeModel;
        $model->propInt = '0';
        self::assertSame(0, $model->propInt);

        $model->propDat = '2013-01-01';
        self::assertInstanceOf('DateTime', $model->propDat);
        Types\TypedObject::$casting = false;
    }

    /**
     * @test
     */
    function extendでスキーマを拡張定義できる() {
        $model = new ChildModel;
        self::assertTrue($model->propAdd);
    }

    /**
     * @test
     */
    function arrayExchange()
    {
        $some = SomeModel::fromArray(array(
            'propInt' => 1
        ));
        self::assertInstanceOf(__NAMESPACE__ . '\SomeModel', $some);
        self::assertSame(1, $some->propInt);

        self::assertTrue(is_array($some->toArray()));
    }
}

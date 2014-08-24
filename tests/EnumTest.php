<?php
namespace Spindle\Types\Tests;

use Spindle\Types;

class Suit extends Types\Enum
{
    const
        SPADE   = 'spade'
      , HEART   = 'heart'
      , CLUB    = 'club'
      , DIAMOND = 'diamond'
    ;
}

class EnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function 正常な使い方()
    {
        $suit = new Suit(Suit::SPADE);
        self::assertSame(Suit::SPADE, (string)$suit);
        self::assertSame(Suit::SPADE, $suit->valueOf());
    }

    /**
     * @test
     */
    function SyntaxSugar()
    {
        $suit = Suit::SPADE();
        self::assertSame(Suit::SPADE, (string)$suit);
        self::assertSame(Suit::SPADE, $suit->valueOf());

        // cached item
        $suit2 = Suit::SPADE();
        self::assertSame($suit, $suit2);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function 定義されていない値はセットできない()
    {
        $suit = new Suit('uso800');
    }
}

<?php
namespace Spindle\Types\Tests;

use Spindle\Types\Polyfill;

class Polyfill_DateTimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function immutable()
    {
        $dateString = '2012-01-01T00:00:00+00:00';
        $DateTimeImmutable = 'Spindle\Types\Polyfill\DateTimeImmutable';

        $iDate = new Polyfill\DateTimeImmutable($dateString);

        $iDate2 = $iDate->modify('tomorrow');
        self::assertNotSame($iDate, $iDate2);
        self::assertInstanceOf($DateTimeImmutable, $iDate2);
        self::assertSame('2012-01-02T00:00:00+00:00', $iDate2->format('c'));

        $iDate3 = $iDate->add(new \DateInterval('P2D'));
        self::assertNotSame($iDate, $iDate3);
        self::assertInstanceOf($DateTimeImmutable, $iDate3);
        self::assertSame('2012-01-03T00:00:00+00:00', $iDate3->format('c'));

        $iDate4 = Polyfill\DateTimeImmutable::createFromFormat(\DateTime::ATOM, $dateString);
        self::assertInstanceOf($DateTimeImmutable, $iDate4);
        self::assertSame($dateString, $iDate4->format(\DateTime::ATOM));

        $iDate4 = Polyfill\DateTimeImmutable::createFromFormat(\DateTime::ATOM, $dateString, new \DateTimeZone('UTC'));
        self::assertInstanceOf($DateTimeImmutable, $iDate4);
        self::assertSame($dateString, $iDate4->format(\DateTime::ATOM));

        $iDate5 = $iDate->setDate(2013, 1, 1);
        self::assertNotSame($iDate, $iDate5);
        self::assertInstanceOf($DateTimeImmutable, $iDate5);

        $iDate6 = $iDate->setISODate(2013, 1, 1);
        self::assertNotSame($iDate, $iDate6);
        self::assertInstanceOf($DateTimeImmutable, $iDate6);

        $iDate7 = $iDate->setTimestamp(0);
        self::assertNotSame($iDate, $iDate7);
        self::assertInstanceOf($DateTimeImmutable, $iDate7);
    }

    /**
     * @test
     */
    function mutable()
    {
        $dateString = '2012-01-01T00:00:00+00:00';
        $DateTime = 'Spindle\Types\Polyfill\DateTime';

        $date = Polyfill\DateTime::createFromFormat(Polyfill\DateTime::ATOM, $dateString);
        self::assertInstanceOf($DateTime, $date);

        $date = Polyfill\DateTime::createFromFormat(Polyfill\DateTime::ATOM, $dateString, new \DateTimeZone('UTC'));
        self::assertInstanceOf($DateTime, $date);

        $exported = var_export($date, true);
        eval('$b = ' . $exported . ';');
        self::assertSame($date->format('c'), $b->format('c'));
    }
}

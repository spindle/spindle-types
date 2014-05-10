<?php
/**
 * spindle/types
 *
 * @license CC0-1.0 (Public Domain) https://creativecommons.org/publicdomain/zero/1.0/
 */
namespace Spindle\Types\Polyfill;

class DateTime extends \DateTime implements DateTimeInterface
{
    static function createFromFormat($format, $time, /* \DateTimeZone */ $timezone=null)
    {
        if ($timezone) {
            $dateTime = \date_create_from_format($format, $time, $timezone);
        } else {
            $dateTime = \date_create_from_format($format, $time);
        }
        return new static($dateTime->format('c'));
    }

    static function __set_state(array $array)
    {
        $dateTime = parent::__set_state($array);
        return new static($dateTime->format('c'));
    }
}

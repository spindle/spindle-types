<?php
/**
 * spindle/types
 *
 * @license CC0-1.0 (Public Domain) https://creativecommons.org/publicdomain/zero/1.0/
 */
namespace Spindle\Types\Polyfill;

//@codeCoverageIgnoreStart
if (class_exists('DateTimeImmutable', false)) {
    class DateTimeImmutable extends \DateTimeImmutable implements DateTimeInterface
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
} else {
    class DateTimeImmutable extends \DateTime implements DateTimeInterface
    {
        function add(/* \DateInterval */ $interval)
        {
            $new = clone $this;
            return \date_add($new, $interval);
        }

        static function createFromFormat($format, $time, /* \DateTimeZone */ $timezone=null)
        {
            if ($timezone) {
                $dateTime = \date_create_from_format($format, $time, $timezone);
            } else {
                $dateTime = \date_create_from_format($format, $time);
            }
            return new static($dateTime->format('c'));
        }

        function modify($modify)
        {
            $new = clone $this;
            return \date_modify($new, $modify);
        }

        function setDate($year, $month, $day)
        {
            $new = clone $this;
            return \date_date_set($new, $year, $month, $day);
        }

        function setISODate($year, $month, $day = 1)
        {
            $new = clone $this;
            return \date_isodate_set($new, $year, $month, $day);
        }

        function setTime($hour, $minute, $second = 0)
        {
            $new = clone $this;
            return \date_time_set($new, $hour, $second);
        }

        function setTimestamp($unixtimestamp)
        {
            $new = clone $this;
            return \date_timestamp_set($new, $unixtimestamp);
        }

        function setTimezone(/* \DateTimeZone */ $timezone)
        {
            $new = clone $this;
            return \date_timezone_set($new, $timezone);
        }

        static function __set_state(array $array)
        {
            $dateTime = parent::__set_state($array);
            return new static($dateTime->format('c'));
        }
    }
}
//@codeCoverageIgnoreEnd

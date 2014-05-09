<?php
/**
 * Spindle\Types\Polyfill
 *
 * @license CC0-1.0 (Public Domain) https://creativecommons.org/publicdomain/zero/1.0/
 */
namespace Spindle\Types\Polyfill;

// @codeCoverageIgnoreStart
if (interface_exists('DateTimeInterface', false)) {
    interface DateTimeInterface extends \DateTimeInterface
    {
    }
} else {
    interface DateTimeInterface
    {
        function diff(DateTimeInterface $datetime2, $absolute = false);
        function format($format);
        function getOffset();
        function getTimestamp();
        function getTimezone();
        function __wakeup();
    }
}
// @codeCoverageIgnoreEnd

<?php
/**
 * Spindle\Types\Polyfill
 *
 * @license CC0-1.0 (Public Domain) https://creativecommons.org/publicdomain/zero/1.0/
 */
namespace Spindle\Types\Polyfill;

/**
 * PHP>=5.4にしか存在しないJsonSerializableのPolyfillクラス。
 */
// @codeCoverageIgnoreStart
if (interface_exists('JsonSerializable', false)) {
    interface JsonSerializable extends \JsonSerializable
    {
    }
} else {
    interface JsonSerializable
    {
        /**
         * @param void
         * @return mixed
         */
        function jsonSerialize();
    }
}
// @codeCoverageIgnoreEnd

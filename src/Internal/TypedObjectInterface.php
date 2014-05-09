<?php
/**
 * spindle/types
 *
 * @license CC0-1.0 (Public Domain) https://creativecommons.org/publicdomain/zero/1.0/
 */
namespace Spindle\Types\Internal;

interface TypedObjectInterface
{
    const
        BOOL = 'boolean'
      , INT  = 'integer'
      , DBL  = 'double'
      , STR  = 'string'
      , CALL = 'callable'
      , ARR  = 'array'
      , RES  = 'resource'
      , OBJ  = 'object'
      , MIX  = 'mixed'
      ;

    /**
     * オブジェクトの構造を定義する
     *
     * @param void
     * @return array
     */
    static function schema();

    /**
     * インスタンスのバリデーションを行う
     *
     * @param void
     * @return array|null
     */
    function checkErrors();
}

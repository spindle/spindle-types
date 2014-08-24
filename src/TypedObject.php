<?php
/**
 * spindle/types
 *
 * @license CC0-1.0 (Public Domain) https://creativecommons.org/publicdomain/zero/1.0/
 */
namespace Spindle\Types;

/**
 * プロパティの型を保証するオブジェクト
 */
abstract class TypedObject implements
    Internal\TypedObjectInterface,
    \IteratorAggregate,
    \Countable
{
    private $_storage = array();

    private static $_schemaCache = array();
    private static $_defaultCache = array();

    public static $preventExtensions = true;
    public static $casting = false;

    final function __construct()
    {
        $class = get_class($this);

        if (isset(self::$_schemaCache[$class])) {
            $this->_storage = self::$_defaultCache[$class];
            goto initialize;
        }

        $schema = $this->schema();
        if (! is_array($schema))
            throw new \DomainException("$class::schema() must return an array.");

        foreach ($schema as $key => $type) {
            if (is_int($key)) {
                if (empty($lastKey))
                    throw new \DomainException("$class::schema() is invalid.");

                $this->_storage[$lastKey] = $type;
                continue;

            } else {
                switch ($type) {
                case self::STR: case self::BOOL: case self::INT: case self::OBJ:
                case self::ARR: case self::RES: case self::DBL: case self::CALL:
                case self::MIX:
                    break;

                default:
                    if (!class_exists($type))
                        throw new \DomainException("$class::schema()[$key] class not found.");
                }
                $this->_storage[$key] = null;
            }

            $lastKey = $key;
        }

        //スキーマをキャッシュする
        self::$_schemaCache[$class] = $schema;

        //デフォルト値をキャッシュする
        self::$_defaultCache[$class] = $this->_storage;

        initialize:
        $initializer = array($this, 'initialize');
        if (is_callable($initializer)) {
            $args = func_get_args();
            //func_get_args()の結果を直接渡すとエラーになる
            call_user_func_array($initializer, $args);
        }
    }

    /**
     * template method
     * function initialize()
     * {
     * }
     */

    /**
     * schema()の定義を継承拡張する
     *
     * @param array $parent parent::schema()の結果
     * @param array $child  拡張するschema定義
     * @return array 継承拡張されたschema定義
     */
    final static function extend(array $parent, array $child)
    {
        //parent側を整理
        $parentSchema = array();
        $lastKey = null;
        foreach ($parent as $key => $val) {
            if (is_int($key)) {
                $parentSchema[$lastKey][] = $val;
            } else {
                $parentSchema[$key][] = $val;
            }

            $lastKey = $key;
        }

        //child側を整理
        $childSchema = array();
        $lastKey = null;
        foreach ($child as $key => $val) {
            if (is_int($key)) {
                $parentSchema[$lastKey][] = $val;
            } else {
                $parentSchema[$key][] = $val;
            }

            $lastKey = $key;
        }

        //マージ
        $mergedSchema = $childSchema + $parentSchema;

        //schemaの形式に復元
        $merged = array();
        foreach ($mergedSchema as $key => $val) {
            $merged[$key] = $val[0];
            if (isset($val[1])) {
                $merged[] = $val[1];
            }
        }

        return $merged;
    }

    final function __get($name)
    {
        if (array_key_exists($name, $this->_storage)) {
            return $this->_storage[$name];
        }

        if (static::$preventExtensions) {
            throw new \OutOfRangeException(get_class($this) . "->$name is not defined.");
        }

        return null;
    }

    final function __isset($name)
    {
        return isset($this->_storage[$name]);
    }

    final function __unset($name)
    {
        if (isset($this->_storage[$name])) {
            $this->_storage[$name] = null;
        }
    }

    final function __set($name, $value)
    {
        $class = get_class($this);
        $schema = self::$_schemaCache[$class];

        if (! array_key_exists($name, $schema)) {
            if (static::$preventExtensions) {
                throw new \OutOfRangeException("$class->$name is not defined.");
            } else {
                $this->_storage[$name] = $value;
                return;
            }
        }

        $type = $schema[$name];
        switch ($type) {
            case self::CALL:
                if (is_callable($value)) {
                    $this->_storage[$name] = $value;
                    return;
                }
                break;
            case self::BOOL: case self::INT: case self::DBL: case self::STR:
            case self::ARR: case self::OBJ: case self::RES:
                if (gettype($value) === $type) {
                    $this->_storage[$name] = $value;
                    return;
                } elseif (static::$casting) {
                    settype($value, $type);
                    $this->_storage[$name] = $value;
                    return;
                }
                break;
            case self::MIX:
                $this->_storage[$name] = $value;
                return;
            default:
                if ($value instanceof $type) {
                    $this->_storage[$name] = $value;
                    return;
                } elseif (static::$casting) {
                    $value = new $type($value);
                    $this->_storage[$name] = $value;
                    return;
                }
        }

        throw new \InvalidArgumentException("$class->$name must be $type.");
    }

    /**
     * エラーチェックのためのメソッド。
     * 要素間の関連であったり、null許可のチェックなどに使ってください。
     *
     * ここではデフォルトとしてnullチェックを行います。
     */
    function checkErrors()
    {
        $errors = array();
        foreach ($this as $name => $val) {
            if ($val === null) {
                $errors[$name][] = 'value is null';
            }
        }

        return $errors;
    }

    /**
     * クラスが持っている属性の一覧を取得します。
     *
     * @return array
     */
    function keys()
    {
        return array_keys($this->_storage);
    }

    /**
     * クラス名を返します。
     *
     * @return string
     */
    static function className()
    {
        return get_called_class();
    }

    /**
     * override \IteratorAggregate::getIterator
     */
    function getIterator()
    {
        return new \ArrayIterator($this->_storage);
    }

    /**
     * override \Countable::count
     */
    function count()
    {
        return count($this->_storage);
    }

    /**
     * @param array $arr
     * @return TypedObject
     */
    static function fromArray(array $arr)
    {
        $self = new static;
        foreach ($arr as $key => $val) {
            $self->__set($key, $val);
        }
        return $self;
    }

    /**
     * @return array;
     */
    function toArray()
    {
        return $this->_storage;
    }
}

spindle/types
=============

[![Build Status](https://travis-ci.org/spindle/spindle-types.svg?branch=master)](https://travis-ci.org/spindle/spindle-types)
[![Latest Stable Version](https://poser.pugx.org/spindle/types/v/stable.png)](https://packagist.org/packages/spindle/types)
[![Total Downloads](https://poser.pugx.org/spindle/types/downloads.png)](https://packagist.org/packages/spindle/types)
[![Latest Unstable Version](https://poser.pugx.org/spindle/types/v/unstable.png)](https://packagist.org/packages/spindle/types)
[![License](https://poser.pugx.org/spindle/types/license.png)](https://packagist.org/packages/spindle/types)

PHPにより強い型付けを提供する基底クラス群です。

```sh
$ composer require 'spindle/types:*'
```

## 基本の型

### Spindle\Types\Enum

Enumを継承すると列挙型になります。インスタンスは、クラスに定義したconstのいずれかの値であることを型から保証することができます。

```php
<?php

final class Suit extends Spindle\Types\Enum
{
    const SPADE   = 'spade'
       ,  CLUB    = 'club'
       ,  HEART   = 'heart'
       ,  DIAMOND = 'diamond'
}

$spade = new Suit(Suit::SPADE);
$spade = Suit::SPADE(); //syntax sugar

echo $spade, PHP_EOL;
echo $spade->valueOf(), PHP_EOL;

function doSomething(Suit $suit)
{
    //$suitは必ず4種類のうちのどれかである
}
```

### Spindle\Types\TypedObject

TypedObjectを継承すると、プロパティの型を固定化したクラスを作ることができます。複雑なデータをより確実に扱うことができます。Domain Driven Designにおける"Entity"や"ValueObject"の実装に使えます。

型は`schema()`というstaticメソッドで定義します。
schemaは配列を返す必要があり、その配列は`プロパティ名 => 型, デフォルト値,`を繰り返したものになります。デフォルト値は省略でき、その場合はnullがセットされます。

```php
<?php
class User extends Spindle\Types\TypedObject
{
    static function schema()
    {
        return array(
            'firstName' => self::STR,
            'lastName'  => self::STR,
            'age'       => self::INT,
            'birthday'  => 'DateTime', new DateTime('1990-01-01'),
        );
    }

    function checkErrors()
    {
        $errors = array();
        if ($this->age < 0) {
            $errors['age'] = 'ageは0以上である必要があります';
        }

        return $errors;
    }
}

$taro = new User;
$taro->firstName = 'Taro';
$taro->lastName = 'Tanaka';
$taro->age = 20;

//$taro->age = '20'; とすると、InvalidArgumentExceptionが発生して停止する
```

型として指定できる値には以下のものがあります。

- self::BOOL (真偽値)
- self::INT  (整数)
- self::DBL  (浮動小数点数)
- self::STR  (文字列)
- self::ARR  (配列)
- self::OBJ  (オブジェクト)
- self::RES  (リソース)
- self::CALL (コールバック関数)
- self::MIX  (型を指定なし)
- className  クラス名/インターフェース名。完全修飾名で指定します。クラス名の場合、instanceofで判定を行います。

`__get()`や`__set()`をfinalで固定化してしまうため、TypedObjectを継承するとクラスが持つ能力を一部奪うことになります。全てのクラスをTypedObjectから派生させて作るような設計は推奨しません。

TypedObjectはforeachに対応しています。(IteratorAggregate)
TypedObjectはcount()関数で要素数を数えることができます。(Countable)

#### TypedObject::$preventExtensions

TypedObjectはデフォルト状態ではschema()で定義されていないプロパティへの代入・参照を拒否します。これはプロパティのタイプミスを発見しやすくする効果がありますが、不便に感じることもあるでしょう。

TypedObject::$preventExtensionsをfalseにすると、未定義のプロパティを拒否せず、自動で拡張するようになります。(デフォルトはtrue)

なお、拡張したプロパティは自動的にmixed(型検査しない)として扱われます。

```php
<?php
use Spindle\Types;

class MyObj extends Types\TypedObject
{
    static function schema()
    {
        return array(
            'a' => self::INT,
            'b' => self::BOOL,
        );
    }

    function checkErrors()
    {
        return array();
    }
}

$obj = new MyObj;

Types\TypedObject::$preventExtensions = false;
$obj->c = 'str'; //エラーは起きない

Types\TypedObject::$preventExtensions = true;
$obj->c = 'str'; //例外発生
```

#### TypedObject::$casting

TypedObjectはプロパティに代入時、schemaと型が違えば例外を発生させます。
しかしPHPの標準的な挙動のように、違う型を代入しようとしたら型キャストを行ってほしい場合もあるでしょう。例えばデータベースから取り出した文字列からオブジェクトを復元したい場合などです。

TypedObject::$castingをtrueにすると、型が違う代入をしようとしても、なるべくキャストを行おうとします。

#### TypedObjectの継承

TypedObjectで作られたクラスを継承する場合、親クラスのschemaは自動的には引き継がれません。extendメソッドを使って明示的に拡張する必要があります。

```php
<?php
class Employee extends Spindle\Types\TypedObject
{
    static function schema()
    {
        return array(
            'id' => self::INT, 0,
            'name' => self::STR,
        );
    }

    function checkErrors()
    {
        return array();
    }
}

class Boss extends Employee
{
    static function schema()
    {
        return self::extend(parent::schema(), array(
            'room' => self::INT,
        ));
    }
}
```

### Spindle\Types\ConstObject

ConstObjectはTypedObjectを変更不可のオブジェクトにするDecoratorです。

```php
<?php
$const = new ConstObject($typedObject);

echo $const->foo; //参照は透過的に可能
//$const->foo = 'moo'; どのプロパティに対しても代入操作は常に例外を発生させる
```

### Spindle\Types\Collection

array()からいくつか制限を追加した配列です。

- 数値の添え字しか許容しない
- 順番が保証される
- 必要に応じて、要素の型も固定できる


### Spindle\Types\ConstCollection

Collectionを読み取り専用にするDecoratorです。


## Polyfill

PHPは5.4や5.5から使えるようになった標準インターフェースがいくつか存在します。
それらをPHP5.3においても使えるようにする目的で、Polyfillを用意しています。

`DateTime implements DateTimeInterface`などの状態を保証するため、独自の名前空間上に配置しています。

### Spindle\Types\Polyfill\JsonSerializable

`JsonSerializable`インターフェース(PHP5.4以降)に相当します。

### Spindle\Types\Polyfill\DateTimeInterface

`DateTimeInterface`インターフェース(PHP5.5以降)に相当します。

### Spindle\Types\Polyfill\DateTime

`DateTimeInterface`をimplementsしたDateTimeです。

### Spindle\Types\Polyfill\DateTimeImmutable

`DateTimeImmutable`(PHP5.5以降)に相当します。状態を変更することができず、modifyやsetTimestampなどのメソッドを作用させると、別のインスタンスを返します。

License
-----------------------------

spindle/typesの著作権は放棄するものとします。
利用に際して制限はありませんし、作者への連絡や著作権表示なども必要ありません。
スニペット的にコードをコピーして使っても問題ありません。

[ライセンスの原文](LICENSE)

CC0-1.0 (No Rights Reserved)
- https://creativecommons.org/publicdomain/zero/1.0/
- http://sciencecommons.jp/cc0/about (Japanese)


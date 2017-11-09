<?php

namespace Ahc\Underscore\Tests;

use Ahc\Underscore\UnderscoreBase as _;

class Stub
{
    public function toArray()
    {
        return ['a', 'b', 'c'];
    }
}

class Json implements \JsonSerializable
{
    public function jsonSerialize()
    {
        return ['a' => 1, 'b' => 2, 'c' => 3];
    }
}

class UnderscoreBaseTest extends \PHPUnit_Framework_TestCase
{
    public function test_asArray()
    {
        $this->assertSame(['one'], (new _)->asArray('one'));
        $this->assertSame([1, 2], (new _)->asArray([1, 2]));
        $this->assertSame(['a', 'b', 'c'], (new _)->asArray(new Stub));
        $this->assertSame(['a', 1, 'c', 3], (new _)->asArray(new _(['a', 1, 'c', 3])));
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], (new _)->asArray(new Json));
    }

    public function test_alias()
    {
        $this->assertTrue(class_exists('Ahc\Underscore'));

        $this->assertEquals(new \Ahc\Underscore\Underscore, new \Ahc\Underscore);
    }

    public function test_underscore()
    {
        $this->assertTrue(function_exists('underscore'));

        $this->assertInstanceOf(_::class, underscore());
    }

    public function test_now()
    {
        $this->assertTrue(is_float(_::_()->now()));
    }

    public function test_keys_values()
    {
        $array = [[1, 2], 'a' => 3, 7, 'b' => 'B'];

        $this->assertSame(array_keys($array), _::_($array)->keys()->get());
        $this->assertSame(array_values($array), _::_($array)->values()->get());
    }

    public function test_pairs()
    {
        $array = ['a' => 3, 7, 'b' => 'B'];

        $this->assertSame(['a' => ['a', 3], 0 => [0, 7], 'b' => ['b', 'B']], _::_($array)->pairs()->get());
    }

    public function test_invert()
    {
        $array = ['a' => 3, 7, 'b' => 'B'];

        $this->assertSame(array_flip($array), _::_($array)->invert()->get());
    }

    public function test_pick_omit()
    {
        $array = _::_(['a' => 3, 7, 'b' => 'B', 1 => ['c', 5]]);

        $this->assertSame([7, 'b' => 'B'], $array->pick([0, 'b'])->get());
        $this->assertSame(['b' => 'B', 1 => ['c', 5]], $array->pick(1, 'b')->get());
        $this->assertSame(['a' => 3, 7], $array->omit([1, 'b'])->get());
        $this->assertSame(['b' => 'B', 1 => ['c', 5]], $array->omit('a', 0)->get());
    }
}
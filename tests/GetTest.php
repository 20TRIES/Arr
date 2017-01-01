<?php namespace _20TRIES\Test;

use _20TRIES\Arr\Arr;
use PHPUnit_Framework_TestCase;

class GetTestTest extends PHPUnit_Framework_TestCase
{
    public function test_get_returns_null_when_no_match_and_no_default_provided()
    {
        $this->assertNull(null, Arr::get([1, 'foo' => ['bar' => ['baz', 2]]], 'foo.bar.baz'));
    }

    public function test_get_returns_default()
    {
        $this->assertEquals('mock-foo', Arr::get([], '', 'mock-foo'));
    }
}
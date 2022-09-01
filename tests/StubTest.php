<?php
/**
 * This file is part of the "sitphp/doubles" package.
 *
 *  @license MIT License
 *  @link https://github.com/sitphp/doubles
 *  @copyright Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 */

namespace Tests;

use Doubles\Constraints;
use \Doubles\Double;
use \Doubles\Exceptions\InvalidArgumentException;
use \Doubles\Stubs;
use \Doubles\TestCase;

class StubTest extends TestCase
{
    /* -----
    Test returnValue
    ---- */
    function testReturnValueShouldReturnValue()
    {
        $double = Double::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->return(Stubs::returnValue('value'));
        $this->assertEquals('value', $double->foo());
    }

    /* -----
    Test returnArgument
    ---- */
    function testReturnArgumentShouldReturnArgument()
    {
        $double = Double::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->return(Stubs::returnArgument(2));
        $this->assertEquals('arg_2', $double->foo('arg_1', 'arg_2'));
    }

    function testReturnArgumentShouldFailWhenArgIndexIsNotValid()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Double::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->return(Stubs::returnArgument('string'));
    }

    function testReturnArgumentShouldFailWhenArgIndexIsNotDefined()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Double::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->return(Stubs::returnArgument(2));
        $double->foo();
    }

    /* -----
    Test returnCallback
    ---- */
    function testReturnCallbackWithAnonymousFunctionShouldReturnCallbackResult()
    {
        $double = Double::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->return(Stubs::returnCallback(function ($a, $b) {
            return $a + $b;
        }));
        $this->assertEquals(5, $double->foo('2', '3'));
    }

    function testReturnCallbackWithUserFunctionShouldReturnCallbackResult()
    {
        $double = Double::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->return(Stubs::returnCallback([new MyCallbackClass(), 'myMethod']));
        $this->assertEquals(5, $double->foo('2', '3'));
    }

    function testReturnCallbackNonValidArgumentShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Double::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->return(Stubs::returnCallback(Stubs::returnValue('value')));
    }

    /* -----
    Test returnSelf
    ---- */
    function testReturnSelfOfInstanceDoubleShouldReturnSelf()
    {
        $double = Double::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->return(Stubs::returnSelf());
        $this->assertSame($double, $double->foo());
    }

    function testReturnSelfOfStaticDoubleShouldReturnSelf()
    {
        $double = Double::mock(StubStandardClass::class)->getClass();
        $double::_method('bar')->return(Stubs::returnSelf());
        $this->assertSame($double, $double::bar());
    }

    /* -----
    Test returnValueMap
    ---- */
    function testReturnValueMapShouldReturnMappedValue()
    {
        $double = Double::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->return(Stubs::returnValueMap([
            ['pink', 'yellow'], ['one', 'two']
        ], ['colors', 'numbers']));
        $this->assertEquals('colors', $double->foo('pink', 'yellow'));
        $this->assertEquals('numbers', $double->foo('one', 'two'));
    }

    function testReturnValueMapWithoutArrayShouldReturnMappedValue()
    {
        $double = Double::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->return(Stubs::returnValueMap([
            ['pink', 'yellow'], ['one', 'two'], 'one'
        ], 'test'));
        $this->assertEquals('test', $double->foo('pink', 'yellow'));
        $this->assertEquals('test', $double->foo('one', 'two'));
        $this->assertEquals('test', $double->foo('one'));
    }

    function testReturnValueMapWithBool()
    {
        $double = Double::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->return(Stubs::returnValueMap([
            [true, false]
        ], ['test']));
        $this->assertEquals('test', $double->foo(true, false));
    }

    function testReturnValueMapWithNull()
    {
        $double = Double::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->return(Stubs::returnValueMap([
            [null, 'arg']
        ], ['test']));
        $this->assertEquals('test', $double->foo(null, 'arg'));
    }

    function testReturnValueMapWithConstraints()
    {
        $double = Double::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->return(Stubs::returnValueMap([
            [Constraints::isTrue(), Constraints::isFalse()]
        ], ['colors']));
        $this->assertEquals('colors', $double->foo(true, false));
        $this->assertEquals('foo', $double->foo(true, 'two'));
    }

    function testReturnValueMapWithNonMappedValue(){
        $double = Double::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->return(Stubs::returnValueMap([
            ['pink', 'yellow'], ['one', 'two']
        ], ['colors', 'numbers']));
        $this->assertEquals('foo',$double->foo('pink', 'red'));
    }

    function testReturnValueMapWithNonMappedValueStatic(){
        $double = Double::mock(StubStandardClass::class)->getClass();
        $double::_method('bar')->return(Stubs::returnValueMap([
            ['pink', 'yellow'], ['one', 'two']
        ], ['colors', 'numbers']));
        $this->assertEquals('foo',$double::bar('pink', 'red'));
    }

    /* -----
    Test throwException
    ---- */
    function testThrowExceptionShouldThrowExceptionOfCorrectType()
    {
        $this->expectException(MyException::class);
        $double = Double::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->return(Stubs::throwException(MyException::class));
        $double->foo();
    }

    function testThrowExceptionWithMessageShouldThrowExceptionOfCorrectType()
    {
        $this->expectException(MyException::class);
        $double = Double::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->return(Stubs::throwException(MyException::class, 'my message'));
        $double->foo();
    }

}

class StubStandardClass
{
    function __construct()
    {

    }

    public function foo()
    {
        return 'foo';
    }

    public static function bar()
    {
        return 'foo';
    }
}

class MyCallbackClass
{
    public function myMethod($a, $b)
    {
        return $a + $b;
    }
}

class MyException extends \Exception
{

}

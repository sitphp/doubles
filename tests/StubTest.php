<?php
/**
 * *
 *  *
 *  * This file is part of the Doublit package.
 *  *
 *  * @license    MIT License
 *  * @link       https://github.com/gealex/doublit
 *  * @copyright  Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 *  *
 *
 */

namespace Tests;

use \Doublit\Doublit;
use \Doublit\Exceptions\InvalidArgumentException;
use \Doublit\Stubs;
use \Doublit\TestCase;

class StubTest extends TestCase
{



    /* -----
    Test returnValue
    ---- */
    function testReturnValueShouldReturnValue()
    {
        $double = Doublit::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->stub(Stubs::returnValue('value'));
        $this->assertEquals('value', $double->foo());
    }

    /* -----
    Test returnArgument
    ---- */
    function testReturnArgumentShouldReturnArgument()
    {
        $double = Doublit::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->stub(Stubs::returnArgument(2));
        $this->assertEquals('arg_2', $double->foo('arg_1', 'arg_2'));
    }

    function testReturnArgumentShouldFailWhenArgIndexIsNotValid()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Doublit::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->stub(Stubs::returnArgument('string'));
    }

    function testReturnArgumentShouldFailWhenArgIndexIsNotDefined()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Doublit::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->stub(Stubs::returnArgument(2));
        $double->foo();
    }

    /* -----
    Test returnCallback
    ---- */
    function testReturnCallbackWithAnonymousFunctionShouldReturnCallbackResult()
    {
        $double = Doublit::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->stub(Stubs::returnCallback(function ($a, $b) {
            return $a + $b;
        }));
        $this->assertEquals(5, $double->foo('2', '3'));
    }

    function testReturnCallbackWithUserFunctionShouldReturnCallbackResult()
    {
        $double = Doublit::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->stub(Stubs::returnCallback([new MyCallbackClass(), 'myMethod']));
        $this->assertEquals(5, $double->foo('2', '3'));
    }

    function testReturnCallbackNonValidArgumentShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Doublit::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->stub(Stubs::returnCallback(Stubs::returnValue('value')));
    }

    /* -----
    Test returnSelf
    ---- */
    function testReturnSelfOfInstanceDoubleShouldReturnSelf()
    {
        $double = Doublit::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->stub(Stubs::returnSelf());
        $this->assertSame($double, $double->foo());
    }

    function testReturnSelfOfStaticDoubleShouldReturnSelf()
    {
        $double = Doublit::mock(StubStandardClass::class)->getClass();
        $double::_method('bar')->stub(Stubs::returnSelf());
        $this->assertSame($double, $double::bar());
    }

    /* -----
    Test returnValueMap
    ---- */
    function testReturnValueMapShouldReturnMappedValue()
    {
        $double = Doublit::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->stub(Stubs::returnValueMap([['pink', 'yellow'], ['one', 'two']], ['colors', 'numbers']));
        $this->assertEquals('colors', $double->foo('pink', 'yellow'));
        $this->assertEquals('numbers', $double->foo('one', 'two'));
    }

    function testReturnValueMapWithWrongReturnCountShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Doublit::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->stub(Stubs::returnValueMap([['pink', 'yellow'], ['one', 'two']], ['colors']));
    }

    /* -----
    Test throwException
    ---- */
    function testThrowExceptionShouldThrowExceptionOfCorrectType()
    {
        $this->expectException(MyException::class);
        $double = Doublit::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->stub(Stubs::throwException(MyException::class));
        $double->foo();
    }

    function testThrowExceptionWithMessageShouldThrowExceptionOfCorrectType()
    {
        $this->expectException(MyException::class);
        $double = Doublit::mock(StubStandardClass::class)->getInstance();
        $double::_method('foo')->stub(Stubs::throwException(MyException::class, 'my message'));
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

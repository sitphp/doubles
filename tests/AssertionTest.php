<?php
/**
 * This file is part of the "sitphp/doubles" package.
 *
 * @license MIT License
 * @link https://github.com/sitphp/doubles
 * @copyright Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 */

namespace SitPHP\Doubles\Tests;

use SitPHP\Doubles\Constraints;
use SitPHP\Doubles\Double;
use SitPHP\Doubles\Exceptions\InvalidArgumentException;
use SitPHP\Doubles\Lib\ExpectationCollection;
use SitPHP\Doubles\Stubs;
use SitPHP\Doubles\TestCase;

class AssertionTest extends TestCase
{
    /* -----
    Test method
    ---- */
    public function testAssertUndefinedMethodShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('undefinedMethod');
    }

    public function testAssertProtectedMethodShouldFailWhenConfigSaySo()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Double::mock(AssertionStandardClass::class, ['allow_protected_methods' => false])->getInstance();
        $double::_method('protect');
    }

    public function testAssertProtectedMethodShouldPassWhenConfigSaySo()
    {
        $double = Double::mock(AssertionStandardClass::class, ['allow_protected_methods' => true])->getInstance();
        $this->assertInstanceOf(ExpectationCollection::class, $double::_method('protect'));
    }

    public function testMethodShouldNotBeAssertedAutomaticallyWhenConfigSaySo()
    {
        $double = Double::mock(AssertionStandardClass::class, ['test_unexpected_methods' => false])->getInstance();
        $this->assertEquals('foo', $double->foo());
    }

    public function testAssertingMethodShouldReturnInstanceOfExpectation()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $expectations = $double::_method('foo');
        $this->assertInstanceOf(ExpectationCollection::class, $expectations);
    }

    public function testAssertingMultipleMethodsShouldReturnInstanceOfExpectationCollection()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $expectations = $double::_method(['foo', 'bar']);
        $this->assertInstanceOf(ExpectationCollection::class, $expectations);
    }

    public function testAssertingInternalMethodsShouldNotBeAllowed()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Double::dummy(DoubleStandardClass::class)->getInstance();
        $double::_method('_double_close');
    }

    /* -----
    Test count
    ---- */
    public function testAssertCountUsingIntZeroComparator()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->count(0);
    }

    public function testAssertCountUsingIntComparators()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->count(3);

        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testAssertCountUsingEqualComparators()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->count('3');

        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testAssertCountUsingGreaterComparators()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->count('>2');

        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testAssertCountUsingGreaterOrEqualComparators()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->count('>=3');

        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testAssertCountUsingLessComparators()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->count('<4');

        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testAssertCountUsingLessOrEqualComparators()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->count('<=3');

        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testAssertCountUsingRangeComparator()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->count('2-5');
        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testAssertCountUsingInvalidRangeComparatorShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->count('2-5-5');
    }

    public function testAssertCountUsingInvalidRangeComparator2ShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->count('2-d');
    }

    public function testAssertCountUsingPhpUnitConstraints()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->count(Constraints::equalTo(3));

        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testAssertCountUsingCustomFunction()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->count(function ($calls) {
            $this->assertEquals(3, count($calls));
        });

        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testPreviousAssertionShouldNotCancelCountAssertion()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo');
        $double::_method('bar')->count(1);

        $double->bar();
    }

    public function testAssertCountUsingInvalidArgumentShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);

        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->count(new \stdClass());
    }

    public function testAssertCountShouldNotTestCountAutomaticallyEvenWhenConfigSaySo()
    {
        $double = Double::mock(AssertionStandardClass::class, null, null, ['test_unexpected_methods' => true])->getInstance();
        $double::_method('foo');
        $this->assertEquals($double->foo(), 'foo');
    }


    /* -----
    Test stub
    ---- */
    public function testAssertStubUsingString()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->return('bar');
        $this->assertEquals('bar', $double->foo());
    }

    public function testAssertStubUsingStubAssertion()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->return(new Stubs\ReturnValueStub('bar'));
        $this->assertEquals('bar', $double->foo());
    }

    public function testAssertStubUsingCustomFunction()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->return(function () {
            return 'bar';
        });
        $this->assertEquals('bar', $double->foo());
    }

    public function testAssertStubWithCallCount()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->return('bar', 2);
        $this->assertEquals('foo', $double->foo());
        $this->assertEquals('bar', $double->foo());
    }

    public function testAssertStubWithMultipleCallCount()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->return('bar', [2, 3]);
        $this->assertEquals('foo', $double->foo());
        $this->assertEquals('bar', $double->foo());
        $this->assertEquals('bar', $double->foo());
        $this->assertEquals('foo', $double->foo());
    }

    public function testAssertStubUsingInvalidCallCountShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->return(AssertionStandardClass::class, 0);
    }

    /* -----
    Test dummy
    ---- */
    public function testAssertDummy()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->dummy();
        $this->assertNull($double->foo());
    }

    public function testAssertDummyWithCallCount()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->dummy(2);
        $this->assertEquals('foo', $double->foo());
        $this->assertNull($double->foo());
    }

    public function testAssertDummyWithMultipleCallCount()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->dummy([2, 3]);
        $this->assertEquals('foo', $double->foo());
        $this->assertNull($double->foo());
        $this->assertNull($double->foo());
    }

    public function testAssertDummyWithInvalidCallCountShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->dummy(0);
    }

    /* -----
    Test mock
    ---- */
    public function testAssertMock()
    {
        $double = Double::dummy(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->mock();
        $this->assertEquals('foo', $double->foo());
    }

    public function testAssertMockWithCallCount()
    {
        $double = Double::dummy(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->mock(2);
        $this->assertNull($double->foo());
        $this->assertEquals('foo', $double->foo());
    }

    public function testAssertMockWithMultipleCallCount()
    {
        $double = Double::dummy(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->mock([2, 3]);
        $this->assertNull($double->foo());
        $this->assertEquals('foo', $double->foo());
        $this->assertEquals('foo', $double->foo());
    }

    public function testAssertMockWithInvalidCallCountShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->mock(0);
    }

    /* -----
    Test default
    ---- */
    public function testAssertDefaultWithDummy()
    {
        $double = Double::dummy(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->mock();
        $double::_method('foo')->default();
        $this->assertNull($double->foo());
    }

    public function testAssertDefaultWithMock()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->dummy();
        $double::_method('foo')->default();
        $this->assertEquals('foo', $double->foo());
    }

    public function testAssertDefaultWithMockWithCount()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->dummy();
        $double::_method('foo')->default(2);
        $this->assertNull($double->foo());
        $this->assertEquals('foo', $double->foo());
        $this->assertNull($double->foo());


    }


    /* -----
    Test arguments
    ---- */
    public function testAssertArgsWithStringValues()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->args(['arg_1', 'arg_2']);
        $double->foo('arg_1', 'arg_2');
    }

    public function testAssertArgsWithBool()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->args([true, false]);
        $double->foo(true, false);
    }

    public function testAssertArgsWithNull()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->args(null);
        $double->foo();
    }

    public function testAssertArgsWithNullArray()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->args([null]);
        $double->foo(null);
    }

    public function testAssertArgsWithNullInArray()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->args(['arg', null]);
        $double->foo('arg', null);
        $double->foo('arg');
    }

    public function testPreviousAssertionShouldNotCancelArgAssertion()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo');
        $double::_method('bar')->args(['arg_1', 'arg_2']);

        $double->bar('arg_1', 'arg_2');
    }

    public function testAssertArgsWithPhpUnitConstraint()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->args([Constraints::equalTo('arg_1'), Constraints::equalTo('arg_2')]);
        $double->foo('arg_1', 'arg_2');
    }

    public function testAssertArgsWithCustomFunction()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->args(function ($arg1, $arg2) {
            $this->assertEquals('arg_1', $arg1);
            $this->assertEquals('arg_2', $arg2);
        });
        $double->foo('arg_1', 'arg_2');
    }

    public function testAssertArgsWithCallCount()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->args(['arg_1', 'arg_2'], 2);
        $double->foo();
        $double->foo('arg_1', 'arg_2');
    }

    public function testAssertArgsWithMultipleCallCount()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->args(['arg_1', 'arg_2'], [2, 3]);
        $double->foo();
        $double->foo('arg_1', 'arg_2');
        $double->foo('arg_1', 'arg_2');
    }

    public function testAssertArgsMethodWithArguments()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('arg')->args([1])->count(1);
        $double->arg(1, false);
    }

    public function testAssertArgsWithInvalidArgumentShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')->args(new \stdClass());
    }


    /* -----
    Test chain
    ---- */

    public function testChain()
    {
        $double = Double::mock(AssertionStandardClass::class)->getInstance();
        $double::_method('foo')
            ->count(2)
            ->args(['arg_1', 'arg_2'], 1)
            ->args(['arg_3', 'arg_4'], 2)
            ->return('return_1', 1)
            ->return('return_2', 2);

        $this->assertEquals('return_1', $double->foo('arg_1', 'arg_2'));
        $this->assertEquals('return_2', $double->foo('arg_3', 'arg_4'));
    }
}

class AssertionStandardClass
{
    function foo()
    {
        return 'foo';
    }

    function bar()
    {
        return 'bar';
    }

    function arg(int $arg1, $arg2 = false)
    {
        return 'bar';
    }

    protected function protect()
    {
        return 'protect';
    }
}
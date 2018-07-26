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

use \Doublit\Stubs;
use \Doublit\Doublit;
use \Doublit\TestCase;
use \Doublit\Constraints;
use \Doublit\Lib\Expectation;
use \Doublit\Lib\ExpectationCollection;
use \Doublit\Exceptions\InvalidArgumentException;

class AssertionTest extends TestCase
{
    /* -----
    Test method
    ---- */
    public function testAssertUndefinedMethodShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('undefinedMethod');
    }

    public function testAssertProtectedMethodShouldFailWhenConfigSaySo()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Doublit::mock_instance(AssertionStandardClass::class, null, null, ['allow_protected_methods' => false]);
        $double::_method('protect');
    }

    public function testAssertProtectedMethodShouldPassWhenConfigSaySo()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class, null, null, ['allow_protected_methods' => true]);
        $this->assertInstanceOf(Expectation::class, $double::_method('protect'));
    }

    public function testMethodShouldNotBeAssertedAutomaticallyWhenConfigSaySo()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class, null, null, ['test_unexpected_methods' => false]);
        $this->assertEquals('foo', $double->foo());
    }

    public function testAssertingMethodShouldReturnInstanceOfExpectation()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $expectations = $double::_method('foo');
        $this->assertInstanceOf(Expectation::class, $expectations);
    }

    public function testAssertingMultipleMethodsShouldReturnInstanceOfExpectationCollection()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $expectations = $double::_method(['foo', 'bar']);
        $this->assertInstanceOf(ExpectationCollection::class, $expectations);
    }

    /* -----
    Test count
    ---- */
    public function testAssertCountUsingIntZeroComparator()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->count(0);
    }

    public function testAssertCountUsingIntComparators()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->count(3);

        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testAssertCountUsingEqualComparators()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->count('3');

        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testAssertCountUsingGreaterComparators()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->count('>2');

        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testAssertCountUsingGreaterOrEqualComparators()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->count('>=3');

        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testAssertCountUsingLessComparators()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->count('<4');

        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testAssertCountUsingLessOrEqualComparators()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->count('<=3');

        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testAssertCountUsingPhpUnitConstraints()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->count(Constraints::equalTo(3));

        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testAssertCountUsingCustomFunction()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->count(function ($calls) {
            $this->assertEquals(3, count($calls));
        });

        $double->foo();
        $double->foo();
        $double->foo();
    }

    public function testPreviousAssertionShouldNotCancelCountAssertion()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo');
        $double::_method('bar')->count(1);

        $double->bar();
    }

    public function testAssertCountUsingInvalidArgumentShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);

        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->count(new \stdClass());
    }

    public function testAssertCountShouldNotTestCountAutomaticallyEvenWhenConfigSaySo()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class, null, null, ['test_unexpected_methods' => true]);
        $double::_method('foo');
        $this->assertEquals($double->foo(),'foo');
    }


    /* -----
    Test stub
    ---- */
    public function testAssertStubUsingString()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->stub('bar');
        $this->assertEquals('bar', $double->foo());
    }

    public function testAssertStubUsingStubAssertion()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $stubs_double = Doublit::mock_name(Stubs::class);
        $stubs_double::_method('returnValue')->stub('bar');
        $double::_method('foo')->stub($stubs_double::returnValue('bar'));
        $this->assertEquals('bar', $double->foo());
    }

    public function testAssertStubUsingCustomFunction()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->stub(function () {
            return 'bar';
        });
        $this->assertEquals('bar', $double->foo());
    }

    public function testAssertStubWithCallCount()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->stub('bar', 2);
        $this->assertEquals('foo', $double->foo());
        $this->assertEquals('bar', $double->foo());
    }

    public function testAssertStubWithMultipleCallCount()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->stub('bar', [2, 3]);
        $this->assertEquals('foo', $double->foo());
        $this->assertEquals('bar', $double->foo());
        $this->assertEquals('bar', $double->foo());
        $this->assertEquals('foo', $double->foo());
    }

    public function testAssertStubUsingWrongArgumentShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->stub(new \stdClass());
    }

    public function testAssertStubUsingInvalidCallCountShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->stub(AssertionStandardClass::class, 0);
    }

    /* -----
    Test dummy
    ---- */
    public function testAssertDummy()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->dummy();
        $this->assertNull($double->foo());
    }

    public function testAssertDummyWithCallCount()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->dummy(2);
        $this->assertEquals('foo', $double->foo());
        $this->assertNull($double->foo());
    }

    public function testAssertDummyWithMultipleCallCount()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->dummy([2, 3]);
        $this->assertEquals('foo', $double->foo());
        $this->assertNull($double->foo());
        $this->assertNull($double->foo());
    }

    public function testAssertDummyWithInvalidCallCountShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->dummy(0);
    }

    /* -----
    Test mock
    ---- */
    public function testAssertMock()
    {
        $double = Doublit::dummy_instance(AssertionStandardClass::class);
        $double::_method('foo')->mock();
        $this->assertEquals('foo', $double->foo());
    }

    public function testAssertMockWithCallCount()
    {
        $double = Doublit::dummy_instance(AssertionStandardClass::class);
        $double::_method('foo')->mock(2);
        $this->assertNull($double->foo());
        $this->assertEquals('foo', $double->foo());
    }

    public function testAssertMockWithMultipleCallCount()
    {
        $double = Doublit::dummy_instance(AssertionStandardClass::class);
        $double::_method('foo')->mock([2, 3]);
        $this->assertNull($double->foo());
        $this->assertEquals('foo', $double->foo());
        $this->assertEquals('foo', $double->foo());
    }

    public function testAssertMockWithInvalidCallCountShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->mock(0);
    }

    /* -----
    Test arguments
    ---- */
    public function testAssertArgsWithStringValues()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->args(['arg_1', 'arg_2']);
        $double->foo('arg_1', 'arg_2');
    }

    public function testPreviousAssertionShouldNotCancelArgAssertion()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo');
        $double::_method('bar')->args(['arg_1', 'arg_2']);

        $double->bar('arg_1', 'arg_2');
    }

    public function testAssertArgsWithNullValue()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->args(null);
        $double->foo();
    }

    public function testAssertArgsWithPhpUnitConstraint()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->args([Constraints::equalTo('arg_1'), Constraints::equalTo('arg_2')]);
        $double->foo('arg_1', 'arg_2');
    }

    public function testAssertArgsWithCustomFunction()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->args(function ($arg1, $arg2) {
            $this->assertEquals('arg_1', $arg1);
            $this->assertEquals('arg_2', $arg2);
        });
        $double->foo('arg_1', 'arg_2');
    }

    public function testAssertArgsWithCallCount()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->args(['arg_1', 'arg_2'], 2);
        $double->foo();
        $double->foo('arg_1', 'arg_2');
    }

    public function testAssertArgsWithMultipleCallCount()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->args(['arg_1', 'arg_2'], [2, 3]);
        $double->foo();
        $double->foo('arg_1', 'arg_2');
        $double->foo('arg_1', 'arg_2');
    }

    public function testAssertArgsWithInvalidArgumentShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->args(new \stdClass());
    }

    public function testAssertArgsWithInvalidCallCountShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')->args(['arg_1', 'arg_2'], 0);
        $double->foo();
        $double->foo('arg_1', 'arg_2');
    }

    public function testChain()
    {
        $double = Doublit::mock_instance(AssertionStandardClass::class);
        $double::_method('foo')
            ->count(2)
            ->args(['arg_1', 'arg_2'], 1)
            ->args(['arg_3', 'arg_4'], 2)
            ->stub('return_1', 1)
            ->stub('return_2', 2);

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

    protected function protect()
    {
        return 'protect';
    }
}

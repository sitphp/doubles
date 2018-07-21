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

use \Doublit\TestCase;
use \Doublit\Lib\DoubleInterface;
use \Doublit\Doublit;
use \Doublit\Exceptions\InvalidArgumentException;

class DoubleTest extends TestCase
{
    /* -----
    Test config
    ---- */
    public function testConfigShouldFailWithInvalidKey()
    {
        $this->expectException(InvalidArgumentException::class);
        Doublit::config('key', 'value');
    }

    public function testConfigShouldFailWithInvalidKeyType()
    {
        $this->expectException(InvalidArgumentException::class);
        Doublit::config(new \stdClass(), 'value');
    }

    /* -----
    Test double
    ---- */
    public function testClassDoubleShouldImplementDoubleInterface()
    {
        $double = Doublit::mock_instance(DoubleStandardClass::class);
        $this->assertInstanceOf(DoubleInterface::class, $double);
    }

    public function testNamedClassDoubleShouldBeInstanceOfNamedClass()
    {
        $double = Doublit::mock_instance('MyClass:' . DoubleStandardClass::class);
        $this->assertInstanceOf('MyClass', $double);
    }

    public function testNamespaceNamedClassDoubleShouldBeInstanceOfNamedClass()
    {
        $double = Doublit::mock_instance('MyNamespacePart1\MyNamespacePart2\MyClass:' . DoubleStandardClass::class);
        $this->assertInstanceOf('MyNamespacePart1\MyNamespacePart2\MyClass', $double);
    }

    public function testInternalClassDoubleShouldImplementItself()
    {
        $double = Doublit::dummy_instance(\ReflectionClass::class);
        $this->assertInstanceOf(\ReflectionClass::class, $double);
    }

    public function testNonExistentClassDoubleShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        Doublit::mock_instance('SomeNonExistentClass');
    }

    public function testClassDoubleWithInvalidTypeShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        Doublit::instance('invalid_type', DoubleStandardClass::class);
    }

    /* -----
    Test named doubles
    ---- */
    public function testNamedClassWithShouldImplementOriginalMethods()
    {
        $double = Doublit::mock_instance('MyNamedClass:' . DoubleStandardClass::class);
        $this->assertEquals('foo', $double->foo());
    }

    public function testNamedDoubleWithAlreadyTakenClassNameShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        Doublit::mock_instance(Doublit::class . ':' . DoubleStandardClass::class);
    }

    /* -----
    Test adding undefined methods
    ---- */
    public function testClassDoubleWithUndefinedMethodsShouldImplementThem()
    {
        $double = Doublit::mock_instance(DoubleStandardClass::class . '[myMethod, static:myOtherMethod]');
        $this->assertNull($double->myMethod());
        $this->assertNull($double::myOtherMethod());
    }

    public function testClassDoubleWithUndefinedMethodsShouldFailWhenMethodIsAlreadyDefined()
    {
        $this->expectException(InvalidArgumentException::class);

        Doublit::mock_instance(DoubleStandardClass::class . '[foo]');
    }


    /* -----
    Test double arguments
    ---- */
    public function testClassDoubleMethodWithArgumentsShouldImplementThem()
    {
        $double = Doublit::mock_instance(DoubleStandardClass::class);
        $this->assertEquals(['one', 'two'], $double->argument('one', 'two'));
    }

    public function testClassDoubleMethodWithReferenceArgumentShouldImplementIt()
    {
        $double = Doublit::mock_instance(DoubleStandardClass::class);
        $a = 1;
        $double->reference($a);
        $this->assertEquals(2, $a);

    }

    public function testClassDoubleMethodWithVariadicArgumentShouldImplementIt()
    {
        $double = Doublit::mock_instance(DoubleStandardClass::class);
        $this->assertEquals([1, 2, 3], $double->variadic(1, 2, 3));
    }

    public function testClassDoubleMethodWithOptionaArgumentShouldImplementIt()
    {
        $double = Doublit::mock_instance(DoubleStandardClass::class);
        $this->assertEquals('optional', $double->optional());
    }

    public function testClassDoubleMethodWithTypeArgumentShouldImplementIt()
    {
        $this->expectException(\TypeError::class);
        $double = Doublit::mock_instance(DoubleStandardClass::class);
        $double->type('string');
    }

    public function testClassDoubleMethodWithClassTypeArgumentShouldImplementIt()
    {
        $this->expectException(\TypeError::class);
        $double = Doublit::mock_instance(DoubleStandardClass::class);
        $double->classType('string');
    }

    public function testClassDoubleMethodWithReturnTypeShouldImplementIt()
    {
        $double = Doublit::mock_instance(DoubleStandardClass::class);
        $this->assertEquals('string', $double->returnType());
    }


    /* -----
    Test mock doubles
    ---- */
    public function testMockDoubleShouldExtendOriginalClass()
    {
        $double = Doublit::mock_instance(DoubleStandardClass::class);
        $this->assertInstanceOf(DoubleStandardClass::class, $double);
    }

    public function testMockDoubleMethodShouldBehaveLikeOriginalClass()
    {
        $double = Doublit::mock_instance(DoubleStandardClass::class);
        $this->assertEquals('foo', $double->foo());
        $this->assertEquals('bar', $double::bar());
    }

    /* -----
    Test dummy doubles
    ---- */
    public function testDummyDoubleShouldExtendOriginalClass()
    {
        $double = Doublit::dummy_instance(DoubleStandardClass::class);
        $this->assertInstanceOf(DoubleStandardClass::class, $double);
    }

    public function testDummyDoubleMethodShouldReturnNull()
    {
        $double = Doublit::dummy_instance(DoubleStandardClass::class);
        $this->assertNull($double->foo());
        $this->assertNull($double::bar());
    }

    /* -----
    Test alias doubles
    ---- */
    public function testNonExistentClassAliasDoubleShouldExtendOriginalClass()
    {
        $double = Doublit::alias_instance('NonExistentClass1', null, null, ['allow_non_existent_classes' => true]);
        $this->assertInstanceOf('NonExistentClass1', $double);
    }

    public function testNonExistentClassAliasDoubleShouldFailWhenConfigSaySo()
    {
        $this->expectException(InvalidArgumentException::class);
        Doublit::alias_instance('OtherNonExistentClass', null, null, ['allow_non_existent_classes' => false]);
    }

    /* -----
    Test classes using trait
    ---- */
    public function testClassUsingTraitDoubleShouldImplementTraitMethod()
    {
        $double = Doublit::dummy_instance(ClassUsingTrait::class);
        $this->assertNull($double->foo());
    }


    /* -----
    Test abstract classes
    ---- */
    public function testAbstractClassDoubleShouldExtendAbstractClass()
    {
        $double = Doublit::mock_instance(AbstractClass::class);
        $this->assertInstanceOf(AbstractClass::class, $double);
    }

    public function testAbstractClassDoubleShouldImplementAbstractMethod()
    {
        $double = Doublit::mock_instance(AbstractClass::class);
        $this->assertNull($double->foo());
    }

    public function testAbstractClassDoubleMethodBehaveLikeOriginalClass()
    {
        $double = Doublit::mock_instance(AbstractClass::class);
        $this->assertEquals('bar', $double->bar());
    }

    /* -----
   Test final classes
   ---- */
    public function testFinalClassDoubleShouldImplementFinalMethodWhenConfigSaySo()
    {
        $double = Doublit::dummy_instance(FinalClass::class, null, null, ['allow_final_doubles' => true]);
        $this->assertNull($double->foo());
    }

    public function testFinalClassWithFinalMethodDoubleShouldNotImplementFinalMethodWhenConfigSaySo()
    {
        $double = Doublit::dummy_instance(ClassWithFinalMethods::class, null, null, ['allow_final_doubles' => false]);
        $this->assertEquals('foo', $double->foo());
    }

    public function testMakingFinalClassDoubleShouldFailWhenConfigSaysSo()
    {
        $this->expectException(InvalidArgumentException::class);
        Doublit::mock_instance(FinalClass::class, null, null, ['allow_final_doubles' => false]);
    }

    /* -----
    Test interfaces
    ---- */
    public function testInterfaceDoubleShouldImplementInterface()
    {
        $double = Doublit::mock_instance(StandardInterface::class);
        $this->assertInstanceOf(StandardInterface::class, $double);
    }

    public function testInterfaceDoubleMethodShouldReturnNull()
    {
        $double = Doublit::mock_instance(StandardInterface::class);
        $this->assertNull($double->foo());
    }

    /* -----
    Test traits
    ---- */
    public function testTraitDoubleShouldImplementOriginalTraitMethod()
    {
        $double = Doublit::dummy_instance(StandardTrait::class);
        $this->assertNull($double->foo());
    }

    public function testTraitWithAbstractShouldImplementAbstractMethod()
    {
        $double = Doublit::mock_instance(TraitWithAbstractMethod::class);
        $this->assertNull($double->foo());
    }

    public function testTraitWithFinalMethodDoubleShouldImplementFinalMethodWhenConfigSaySo()
    {
        $double = Doublit::dummy_instance(TraitWithFinalMethod::class, null, null, ['allow_final_doubles' => true]);
        $this->assertNull($double->foo());
    }

    public function testTraitWithFinalMethodDoubleShouldNotImplementFinalMethodWhenConfigSaySo()
    {
        $double = Doublit::dummy_instance(TraitWithFinalMethod::class, null, null, ['allow_final_doubles' => false]);
        $this->assertEquals('foo', $double->foo());
    }

    /* -----
    Test class with constructor
    ---- */
    public function testClassWithConstructorDummyDoubleShouldNotExecuteOriginalConstructor()
    {
        $double = Doublit::dummy_instance(ClassWithConstructor::class);
        $this->assertEquals('foo', $double->foo);
    }

    public function testClassWithConstructorDummyDoubleWithConstructorArgumentsShouldExecuteOriginalConstructor()
    {
        $double = Doublit::dummy_instance(ClassWithConstructor::class, ['bar']);
        $this->assertEquals('bar', $double->foo);
    }

    public function testClassWithConstructorDoubleWithWrongNumberOfConstructorArgumentsShouldFail()
    {
        if (version_compare(PHP_VERSION, '7.1.0') < 0) {
            $this->expectException(\PHPUnit\Framework\Error\Warning::class);
        } else {
            $this->expectException(\ArgumentCountError::class);
        }
        $double_class = Doublit::mock_name(ClassWithConstructor::class);
        $double = new $double_class();
    }

    /* -----
    Test class with implements
    ---- */
    public function testClassDoubleShouldImplementInterface()
    {
        $double = Doublit::dummy_instance(DoubleStandardClass::class, null, StandardInterface::class);
        $this->assertInstanceOf(StandardInterface::class, $double);
    }

    public function testClassDoubleShouldImplementTrait()
    {
        $double = Doublit::dummy_instance(DoubleStandardClass::class, null, StandardTrait::class);
        $this->assertEquals(StandardTrait::class, class_uses($double)[StandardTrait::class]);
    }

}

class DoubleStandardClass
{
    public function foo()
    {
        return 'foo';
    }

    public static function bar()
    {
        return 'bar';
    }

    public function argument($a, $b)
    {
        return [$a, $b];
    }

    public function type(array $a)
    {
        return $a;
    }

    public function classType(ClassWithConstructor $a)
    {
        return $a;
    }

    public function reference(&$a)
    {
        $a++;
    }

    public function variadic(...$a)
    {
        return $a;
    }

    public function optional($a = 'optional')
    {
        return $a;
    }

    public function returnType(): string
    {
        return 'string';
    }
}

class ClassWithConstructor
{
    public $foo = 'foo';

    public function __construct($foo)
    {
        $this->foo = $foo;
    }
}

class ClassWithFinalMethods
{
    final public function foo()
    {
        return 'foo';
    }
}

final class FinalClass
{
    public function foo()
    {
        return 'foo';
    }
}

abstract class AbstractClass
{
    abstract function foo();

    function bar()
    {
        return 'bar';
    }
}

interface StandardInterface
{
    function foo();
}

trait StandardTrait
{
    function foo()
    {
        return 'foo';
    }
}

trait TraitWithAbstractMethod
{
    abstract function foo();
}

trait TraitWithFinalMethod
{
    final function foo()
    {
        return 'foo';
    }
}

class ClassUsingTrait
{
    use StandardTrait;
}

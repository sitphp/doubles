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
        Doublit::mock(ClassWithConstructor::class, ['invalid_config' => true]);
    }

    function testConfigMappingShouldFailWithNonExistentMethod()
    {
        $this->expectException(InvalidArgumentException::class);
        Doublit::setConfigMapping('label', 'non_existent_method');
    }

    /* -----
    Test double
    ---- */
    public function testClassDoubleShouldImplementDoubleInterface()
    {
        $double = Doublit::dummy(DoubleStandardClass::class)->getInstance();
        $this->assertInstanceOf(DoubleInterface::class, $double);
    }

    public function testNamedClassDoubleShouldBeInstanceOfNamedClass()
    {
        $double = Doublit::dummy(DoubleStandardClass::class)
            ->setName('MyClass')
            ->getInstance();
        $this->assertInstanceOf('MyClass', $double);
    }

    public function testNamespaceNamedClassDoubleShouldBeInstanceOfNamedClass()
    {
        $double = Doublit::dummy(DoubleStandardClass::class)
            ->setName('MyNamespacePart1\MyNamespacePart2\MyClass')
            ->getInstance();
        $this->assertInstanceOf('MyNamespacePart1\MyNamespacePart2\MyClass', $double);
    }

    public function testInternalClassDoubleShouldImplementItself()
    {
        $double = Doublit::dummy(\ReflectionClass::class)->getInstance();
        $this->assertInstanceOf(\ReflectionClass::class, $double);
    }

    public function testNonExistentClassDoubleShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        Doublit::dummy('SomeNonExistentClass')->getInstance();
    }

    public function testClassDoubleOfFinalInternalClassShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        Doublit::dummy(\Closure::class)->getInstance();
    }

    /* -----
    Test named doubles
    ---- */
    public function testNamedClassWithShouldImplementOriginalMethods()
    {
        $double = Doublit::mock(DoubleStandardClass::class)
            ->setName('MyNamedClass')
            ->getInstance();
        $this->assertEquals('foo', $double->foo());
    }

    public function testNamedDoubleWithAlreadyTakenClassNameShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        Doublit::dummy(DoubleStandardClass::class)
            ->setName(Doublit::class)
            ->getInstance();
    }

    /* -----
    Test adding undefined methods
    ---- */
    public function testClassDoubleWithUndefinedMethodsShouldImplementThem()
    {
        $double = Doublit::dummy(DoubleStandardClass::class)
            ->addMethod(['myMethod', 'static:myOtherMethod'])
            ->getInstance();
        $this->assertNull($double->myMethod());
        $this->assertNull($double::myOtherMethod());
    }

    public function testClassDoubleWithUndefinedMethodsShouldFailWhenMethodIsAlreadyDefined()
    {
        $this->expectException(InvalidArgumentException::class);

        Doublit::dummy(DoubleStandardClass::class)
            ->addMethod('foo')
            ->getInstance();
    }


    /* -----
    Test double arguments
    ---- */
    public function testClassDoubleMethodWithArgumentsShouldImplementThem()
    {
        $double = Doublit::mock(DoubleStandardClass::class)->getInstance();
        $this->assertEquals(['one', 'two'], $double->argument('one', 'two'));
    }

    public function testClassDoubleMethodWithReferenceArgumentShouldImplementIt()
    {
        $double = Doublit::mock(DoubleStandardClass::class)->getInstance();
        $a = 1;
        $double->reference($a);
        $this->assertEquals(2, $a);

    }

    public function testClassDoubleMethodWithVariadicArgumentShouldImplementIt()
    {
        $double = Doublit::mock(DoubleStandardClass::class)->getInstance();
        $this->assertEquals([1, 2, 3], $double->variadic(1, 2, 3));
    }

    public function testClassDoubleMethodWithDefaultArgumentShouldImplementIt()
    {
        $double = Doublit::mock(DoubleStandardClass::class)->getInstance();
        $this->assertEquals(1, $double->defaultWithOptional());
    }

    public function testClassDoubleMethodWithOptionalSlashedArgumentsShouldBeCorrect()
    {
        $double = Doublit::mock(DoubleStandardClass::class)->getInstance();
        $this->assertEquals('\\', $double->defaultWithEscape());
    }

    public function testClassDoubleMethodWithTypeArgumentShouldImplementIt()
    {
        $this->expectException(\TypeError::class);
        $double = Doublit::mock(DoubleStandardClass::class)->getInstance();
        $double->type('string');
    }

    public function testClassDoubleMethodWithClassTypeArgumentShouldImplementIt()
    {
        $this->expectException(\TypeError::class);
        $double = Doublit::mock(DoubleStandardClass::class)->getInstance();
        $double->classType('string');
    }

    public function testClassDoubleMethodWithReturnTypeShouldImplementIt()
    {
        $double = Doublit::mock(DoubleStandardClass::class)->getInstance();
        $this->assertEquals('string', $double->returnType());
    }


    /* -----
    Test mock doubles
    ---- */
    public function testMockDoubleShouldExtendOriginalClass()
    {
        $double = Doublit::mock(DoubleStandardClass::class)->getInstance();
        $this->assertInstanceOf(DoubleStandardClass::class, $double);
    }

    public function testMockDoubleMethodShouldBehaveLikeOriginalClass()
    {
        $double = Doublit::mock(DoubleStandardClass::class)->getInstance();
        $this->assertEquals('foo', $double->foo());
        $this->assertEquals('bar', $double::bar());
    }

    /* -----
    Test dummy doubles
    ---- */
    public function testDummyDoubleShouldExtendOriginalClass()
    {
        $double = Doublit::dummy(DoubleStandardClass::class)->getInstance();
        $this->assertInstanceOf(DoubleStandardClass::class, $double);
    }

    public function testDummyDoubleMethodShouldReturnNull()
    {
        $double = Doublit::dummy(DoubleStandardClass::class)->getInstance();
        $this->assertNull($double->foo());
        $this->assertNull($double::bar());
    }

    /* -----
    Test alias doubles
    ---- */
    public function testNonExistentClassAliasDoubleShouldExtendOriginalClass()
    {
        $double = Doublit::alias('NonExistentClass1', ['allow_non_existent_classes' => true])->getInstance();
        $this->assertInstanceOf('NonExistentClass1', $double);
    }

    public function testNonExistentClassAliasDoubleShouldFailWhenConfigSaySo()
    {
        $this->expectException(InvalidArgumentException::class);
        Doublit::alias('OtherNonExistentClass', ['allow_non_existent_classes' => false])->getInstance();
    }

    /* -----
    Test classes using trait
    ---- */
    public function testClassUsingTraitDoubleShouldImplementTraitMethod()
    {
        $double = Doublit::dummy(ClassUsingTrait::class)->getInstance();
        $this->assertNull($double->foo());
    }


    /* -----
    Test abstract classes
    ---- */
    public function testAbstractClassDoubleShouldExtendAbstractClass()
    {
        $double = Doublit::mock(AbstractClass::class)->getInstance();
        $this->assertInstanceOf(AbstractClass::class, $double);
    }

    public function testAbstractClassDoubleShouldImplementAbstractMethod()
    {
        $double = Doublit::mock(AbstractClass::class)->getInstance();
        $this->assertNull($double->foo());
    }

    public function testAbstractClassDoubleMethodBehaveLikeOriginalClass()
    {
        $double = Doublit::mock(AbstractClass::class)->getInstance();
        $this->assertEquals('bar', $double->bar());
    }

    /* -----
   Test final classes
   ---- */
    public function testFinalClassDoubleShouldImplementFinalMethodWhenConfigSaySo()
    {
        $double = Doublit::dummy(FinalClass::class, ['allow_final_doubles' => true])->getInstance();
        $this->assertNull($double->foo());
    }

    public function testFinalClassWithFinalMethodDoubleShouldNotImplementFinalMethodWhenConfigSaySo()
    {
        $double = Doublit::dummy(ClassWithFinalMethods::class, ['allow_final_doubles' => false])->getInstance();
        $this->assertEquals('foo', $double->foo());
    }

    public function testMakingFinalClassDoubleShouldFailWhenConfigSaysSo()
    {
        $this->expectException(InvalidArgumentException::class);
        Doublit::dummy(FinalClass::class, ['allow_final_doubles' => false])->getInstance();
    }

    /* -----
    Test interfaces
    ---- */
    public function testInterfaceDoubleShouldImplementInterface()
    {
        $double = Doublit::mock(StandardInterface::class)->getInstance();
        $this->assertInstanceOf(StandardInterface::class, $double);
    }

    public function testInterfaceDoubleMethodShouldReturnNull()
    {
        $double = Doublit::mock(StandardInterface::class)->getInstance();
        $this->assertNull($double->foo());
    }

    /* -----
    Test traits
    ---- */
    public function testTraitDoubleShouldImplementOriginalTraitMethod()
    {
        $double = Doublit::dummy(StandardTrait::class)->getInstance();
        $this->assertNull($double->foo());
    }

    public function testTraitWithAbstractShouldImplementAbstractMethod()
    {
        $double = Doublit::mock(TraitWithAbstractMethod::class)->getInstance();
        $this->assertNull($double->foo());
    }

    public function testTraitWithFinalMethodDoubleShouldImplementFinalMethodWhenConfigSaySo()
    {
        $double = Doublit::dummy(TraitWithFinalMethod::class, ['allow_final_doubles' => true])->getInstance();
        $this->assertNull($double->foo());
    }

    public function testTraitWithFinalMethodDoubleShouldNotImplementFinalMethodWhenConfigSaySo()
    {
        $double = Doublit::dummy(TraitWithFinalMethod::class, ['allow_final_doubles' => false])->getInstance();
        $this->assertEquals('foo', $double->foo());
    }

    /* -----
    Test class with constructor
    ---- */
    public function testClassWithConstructorMockDoubleShouldExecuteOriginalConstructor()
    {
        $double = Doublit::mock(ClassWithConstructor::class)->getInstance('bar');
        $this->assertEquals('bar', $double->foo);
    }

    public function testClassWithoutConstructorMockDoubleShouldFail()
    {
        if (version_compare(PHP_VERSION, '7.1.0') < 0) {
            $this->expectException(\PHPUnit\Framework\Error\Warning::class);
        } else {
            $this->expectException(\ArgumentCountError::class);
        }
        $double = Doublit::mock(ClassWithConstructor::class)->getInstance();
        $this->assertEquals('foo', $double->foo);
    }

    public function testClassWithConstructorDummyDoubleShouldNotExecuteOriginalConstructor()
    {
        $double = Doublit::dummy(ClassWithConstructor::class)->getInstance();
        $this->assertEquals('foo', $double->foo);
    }

    public function testClassWithConstructorDummyDoubleWithConstructorArgumentsShouldNotExecuteOriginalConstructor()
    {
        $double = Doublit::dummy(ClassWithConstructor::class)->getInstance(['bar']);
        $this->assertEquals('foo', $double->foo);
    }

    public function testClassWithConstructorDoubleWithWrongNumberOfConstructorArgumentsShouldFail()
    {
        if (version_compare(PHP_VERSION, '7.1.0') < 0) {
            $this->expectException(\PHPUnit\Framework\Error\Warning::class);
        } else {
            $this->expectException(\ArgumentCountError::class);
        }
        Doublit::mock(ClassWithConstructor::class)->getInstance();
    }


    /* -----
    Test class with implements
    ---- */
    public function testClassDoubleShouldImplementInterface()
    {
        $double = Doublit::dummy(DoubleStandardClass::class)
            ->addInterface(StandardInterface::class)
            ->getInstance();
        $this->assertInstanceOf(StandardInterface::class, $double);
    }

    public function testClassDoubleShouldImplementTrait()
    {
        $double = Doublit::dummy(DoubleStandardClass::class)->addTrait(StandardTrait::class)->getInstance();
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

    public function reference(&$a = 1)
    {
        $a++;
    }

    public function variadic(...$a)
    {
        return $a;
    }

    public function defaultWithOptional($default = null, $a = 1, $optional = null)
    {
        return $a;
    }

    public function defaultWithStringType(string $a = 'optional')
    {
        return $a;
    }

    public function defaultWithIntType(int $a = 0)
    {
        return $a;
    }

    public function defaultWithBoolType(bool $a = true)
    {
        return $a;
    }

    public function defaultWithArrayType(array $a = ['ar"ray1', 'array2'])
    {
        return $a;
    }

    public function defaultWithFloatType(float $a = 1.3)
    {
        return $a;
    }

    public function defaultWithSelfType(self $a)
    {
        return $a;
    }

    public function defaultWithCallableType(callable $a)
    {
        return $a;
    }

    public function defaultWithSingleQuote($a = "'")
    {
        return $a;
    }

    public function defaultWithDoubleQuote($a = '"')
    {
        return $a;
    }

    public function defaultWithEscape($a = "\\")
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

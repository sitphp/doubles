<?php
/**
 * This file is part of the "sitphp/doubles" package.
 *
 * @license MIT License
 * @link https://github.com/sitphp/doubles
 * @copyright Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 */

namespace SitPHP\Doubles\Tests;

use SitPHP\Doubles\Double;
use SitPHP\Doubles\Exceptions\InvalidArgumentException;
use SitPHP\Doubles\Exceptions\LogicException;
use SitPHP\Doubles\Lib\DoubleInterface;
use SitPHP\Doubles\TestCase;

class DoubleTest extends TestCase
{
    /* -----
    Test config
    ---- */
    public function testConfigShouldFailWithInvalidKey()
    {
        $this->expectException(InvalidArgumentException::class);
        Double::mock(ClassWithConstructor::class, ['invalid_config' => true]);
    }

    function testConfigMappingShouldFailWithNonExistentMethod()
    {
        $this->expectException(InvalidArgumentException::class);
        Double::setConfigMapping('label', 'non_existent_method');
    }

    /* -----
    Test double
    ---- */
    public function testClassDoubleShouldImplementDoubleInterface()
    {
        $double = Double::dummy(DoubleStandardClass::class)->getInstance();
        $this->assertInstanceOf(DoubleInterface::class, $double);
    }

    public function testNamedClassDoubleShouldBeInstanceOfNamedClass()
    {
        $double = Double::dummy(DoubleStandardClass::class)
            ->setName('MyClass')
            ->getInstance();
        $this->assertInstanceOf('MyClass', $double);
    }

    public function testNamespaceNamedClassDoubleShouldBeInstanceOfNamedClass()
    {
        $double = Double::dummy(DoubleStandardClass::class)
            ->setName('MyNamespacePart1\MyNamespacePart2\MyClass')
            ->getInstance();
        $this->assertInstanceOf('MyNamespacePart1\MyNamespacePart2\MyClass', $double);
    }

    public function testInternalClassDoubleShouldImplementItself()
    {
        $double = Double::dummy(\ReflectionClass::class)->getInstance();
        $this->assertInstanceOf(\ReflectionClass::class, $double);
    }

    public function testNonExistentClassDoubleShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        Double::dummy('SomeNonExistentClass')->getInstance();
    }

    public function testClassDoubleOfFinalInternalClassShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        Double::dummy(\Closure::class)->getInstance();
    }

    public function testMakingDoubleOfDoubleShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = Double::dummy(DoubleStandardClass::class)->getClass();
        Double::dummy($double)->getClass();
    }

    /* -----
    Test named doubles
    ---- */
    public function testNamedClassWithShouldImplementOriginalMethods()
    {
        $double = Double::mock(DoubleStandardClass::class)
            ->setName('MyNamedClass')
            ->getInstance();
        $this->assertEquals('foo', $double->foo());
    }

    public function testNamedDoubleWithAlreadyTakenClassNameShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        Double::dummy(DoubleStandardClass::class)
            ->setName(Double::class)
            ->getInstance();
    }

    /* -----
    Test adding undefined methods
    ---- */
    public function testClassDoubleWithUndefinedMethodsShouldImplementThem()
    {
        $double = Double::dummy(DoubleStandardClass::class)
            ->addMethod(['myMethod', 'static:myOtherMethod'])
            ->getInstance();
        $this->assertNull($double->myMethod());
        $this->assertNull($double::myOtherMethod());
    }

    public function testClassDoubleWithUndefinedMethodsShouldFailWhenMethodIsAlreadyDefined()
    {
        $this->expectException(InvalidArgumentException::class);

        Double::dummy(DoubleStandardClass::class)
            ->addMethod('foo')
            ->getInstance();
    }


    /* -----
    Test double arguments
    ---- */
    public function testClassDoubleMethodWithArgumentsShouldImplementThem()
    {
        $double = Double::mock(DoubleStandardClass::class)->getInstance();
        $this->assertEquals(['one', 'two'], $double->argument('one', 'two'));
    }

    public function testClassDoubleMethodWithReferenceArgumentShouldImplementIt()
    {
        $double = Double::mock(DoubleStandardClass::class)->getInstance();
        $a = 1;
        $double->reference($a);
        $this->assertEquals(2, $a);

    }

    public function testClassDoubleMethodWithVariadicArgumentShouldImplementIt()
    {
        $double = Double::mock(DoubleStandardClass::class)->getInstance();
        $this->assertEquals([1, 2, 3], $double->variadic(1, 2, 3));
    }

    public function testClassDoubleMethodWithDefaultArgumentShouldImplementIt()
    {
        $double = Double::mock(DoubleStandardClass::class)->getInstance();
        $this->assertEquals(1, $double->defaultWithOptional());
    }

    public function testClassDoubleMethodWithOptionalSlashedArgumentsShouldBeCorrect()
    {
        $double = Double::mock(DoubleStandardClass::class)->getInstance();
        $this->assertEquals('\\', $double->defaultWithEscape());
    }

    public function testClassDoubleMethodWithTypeArgumentShouldImplementIt()
    {
        $this->expectException(\TypeError::class);
        $double = Double::mock(DoubleStandardClass::class)->getInstance();
        $double->type('string');
    }

    public function testClassDoubleMethodWithClassTypeArgumentShouldImplementIt()
    {
        $this->expectException(\TypeError::class);
        $double = Double::mock(DoubleStandardClass::class)->getInstance();
        $double->classType('string');
    }

    public function testClassDoubleMethodWithReturnTypeShouldImplementIt()
    {
        $double = Double::mock(DoubleStandardClass::class)->getInstance();
        $this->assertEquals('string', $double->returnType());
    }


    /* -----
    Test mock doubles
    ---- */
    public function testMockDoubleShouldExtendOriginalClass()
    {
        $double = Double::mock(DoubleStandardClass::class)->getInstance();
        $this->assertInstanceOf(DoubleStandardClass::class, $double);
    }

    public function testMockDoubleMethodShouldBehaveLikeOriginalClass()
    {
        $double = Double::mock(DoubleStandardClass::class)->getInstance();
        $this->assertEquals('foo', $double->foo());
        $this->assertEquals('bar', $double::bar());
    }

    /* -----
    Test dummy doubles
    ---- */
    public function testDummyDoubleShouldExtendOriginalClass()
    {
        $double = Double::dummy(DoubleStandardClass::class)->getInstance();
        $this->assertInstanceOf(DoubleStandardClass::class, $double);
    }

    public function testDummyDoubleMethodShouldReturnNull()
    {
        $double = Double::dummy(DoubleStandardClass::class)->getInstance();
        $this->assertNull($double->foo());
        $this->assertNull($double::bar());
    }

    /* -----
    Test alias doubles
    ---- */
    public function testNonExistentClassAliasDoubleShouldExtendOriginalClass()
    {
        $double = Double::alias('NonExistentClass1')->getInstance();
        $this->assertInstanceOf('NonExistentClass1', $double);
    }

    public function testNonExistentClassAliasDoubleShouldFailWhenConfigSaySo()
    {
        $this->expectException(InvalidArgumentException::class);
        Double::alias('OtherNonExistentClass', ['allow_non_existent_classes' => false])->getInstance();
    }

    public function testTraitAlias()
    {
        $double = Double::alias('NonExistentTrait')
            ->aliasTrait()
            ->getClass();
        $this->assertTrue(trait_exists($double));
    }

    public function testAbstractAlias()
    {
        $double = Double::alias('NonExistentAbstract')
            ->aliasAbstract()
            ->getClass();
        $this->assertTrue(class_exists($double));
    }

    public function testAliasTraitShouldFailWithoutAlias()
    {
        $this->expectException(LogicException::class);
        $double = Double::dummy('NonExistentTrait')->aliasTrait();
    }

    public function testAliasAbstractShouldFailWithoutAlias()
    {
        $this->expectException(LogicException::class);
        $double = Double::dummy('NonExistentTrait')->aliasAbstract();
    }

    public function testAliasDoubleShouldFailWhenClassWasAlreadyLoaded()
    {
        $this->expectException(InvalidArgumentException::class);
        $double = new DoubleStandardClass();
        Double::alias(DoubleStandardClass::class, ['allow_non_existent_classes' => true])->getInstance();
    }

    /* -----
    Test classes using trait
    ---- */
    public function testClassUsingTraitDoubleShouldImplementTraitMethod()
    {
        $double = Double::dummy(ClassUsingTrait::class)->getInstance();
        $this->assertNull($double->foo());
    }


    /* -----
    Test abstract classes
    ---- */
    public function testAbstractClassDoubleShouldExtendAbstractClass()
    {
        $double = Double::mock(AbstractClass::class)->getInstance();
        $this->assertInstanceOf(AbstractClass::class, $double);
    }

    public function testAbstractClassDoubleShouldImplementAbstractMethod()
    {
        $double = Double::mock(AbstractClass::class)->getInstance();
        $this->assertNull($double->foo());
    }

    public function testAbstractClassDoubleMethodBehaveLikeOriginalClass()
    {
        $double = Double::mock(AbstractClass::class)->getInstance();
        $this->assertEquals('bar', $double->bar());
    }

    /* -----
   Test final classes
   ---- */
    public function testFinalClassDoubleShouldNotImplementFinalMethodWhenConfigSaySo()
    {
        $double = Double::dummy(FinalClass::class, ['allow_final_doubles' => true])->getInstance();
        $this->assertNull($double->foo());
    }

    public function testMakingFinalClassDoubleShouldFailWhenConfigSaysSo()
    {
        $this->expectException(InvalidArgumentException::class);
        Double::dummy(FinalClass::class, ['allow_final_doubles' => false])->getInstance();
    }

    public function testClassWithFinalMethod()
    {
        $double = Double::dummy(ClassWithFinalMethods::class)->getInstance();
        $this->assertNotInstanceOf(ClassWithFinalMethods::class, $double);
        $this->assertNull($double->foo());
        $this->assertNull($double::bar());
    }

    public function testClassWithFinalMethodDoubleShouldExtendItselfWhenConfigSaysSo()
    {
        $double = Double::dummy(ClassWithFinalMethods::class, ['allow_final_doubles' => false])->getInstance();
        $this->assertInstanceOf(ClassWithFinalMethods::class, $double);
        $this->assertEquals('foo', $double->foo());
        $this->assertEquals('bar', $double::bar());
    }


    /* -----
    Test interfaces
    ---- */
    public function testInterfaceDoubleShouldImplementInterface()
    {
        $double = Double::mock(StandardInterface::class)->getInstance();
        $this->assertInstanceOf(StandardInterface::class, $double);
    }

    public function testInterfaceDoubleMethodShouldReturnNull()
    {
        $double = Double::mock(StandardInterface::class)->getInstance();
        $this->assertNull($double->foo());
    }

    /* -----
    Test traits
    ---- */
    public function testTraitDoubleShouldImplementOriginalTraitMethod()
    {
        $double = Double::dummy(StandardTrait::class)->getInstance();
        $this->assertNull($double->foo());
    }

    public function testTraitWithAbstractShouldImplementAbstractMethod()
    {
        $double = Double::mock(TraitWithAbstractMethod::class)->getInstance();
        $this->assertNull($double->foo());
    }

    public function testTraitWithFinalMethodDoubleShouldImplementFinalMethodWhenConfigSaySo()
    {
        $double = Double::dummy(TraitWithFinalMethod::class, ['allow_final_doubles' => true])->getInstance();
        $this->assertNull($double->foo());
    }

    public function testTraitWithFinalMethodDoubleShouldNotImplementFinalMethodWhenConfigSaySo()
    {
        $double = Double::dummy(TraitWithFinalMethod::class, ['allow_final_doubles' => false])->getInstance();
        $this->assertEquals('foo', $double->foo());
    }

    /* -----
    Test class with constructor
    ---- */
    public function testClassWithConstructorMockDoubleShouldExecuteOriginalConstructor()
    {
        $double = Double::mock(ClassWithConstructor::class)->getInstance('bar');
        $this->assertEquals('bar', $double->foo);
    }

    public function testClassWithoutConstructorMockDoubleShouldFail()
    {
        if (version_compare(PHP_VERSION, '7.1.0') < 0) {
            $this->expectException(\PHPUnit\Framework\Error\Warning::class);
        } else {
            $this->expectException(\ArgumentCountError::class);
        }
        $double = Double::mock(ClassWithConstructor::class)->getInstance();
        $this->assertEquals('foo', $double->foo);
    }

    public function testClassWithConstructorDummyDoubleShouldNotExecuteOriginalConstructor()
    {
        $double = Double::dummy(ClassWithConstructor::class)->getInstance();
        $this->assertEquals('foo', $double->foo);
    }

    public function testClassWithConstructorDummyDoubleWithConstructorArgumentsShouldNotExecuteOriginalConstructor()
    {
        $double = Double::dummy(ClassWithConstructor::class)->getInstance(['bar']);
        $this->assertEquals('foo', $double->foo);
    }

    public function testClassWithConstructorDoubleWithWrongNumberOfConstructorArgumentsShouldFail()
    {
        if (version_compare(PHP_VERSION, '7.1.0') < 0) {
            $this->expectException(\PHPUnit\Framework\Error\Warning::class);
        } else {
            $this->expectException(\ArgumentCountError::class);
        }
        Double::mock(ClassWithConstructor::class)->getInstance();
    }


    /* -----
    Test add interface/trait
    ---- */
    public function testClassDoubleShouldImplementInterface()
    {
        $double = Double::dummy(DoubleStandardClass::class)
            ->addInterface(StandardInterface::class)
            ->getInstance();
        $this->assertInstanceOf(StandardInterface::class, $double);
    }

    public function testAddInterfaceShouldAcceptArrayOfInterfaces()
    {
        $double = Double::dummy(DoubleStandardClass::class)
            ->addInterface([StandardInterface::class, StandardOtherInterface::class])
            ->getInstance();
        $this->assertInstanceOf(StandardInterface::class, $double);
        $this->assertInstanceOf(StandardOtherInterface::class, $double);
    }

    public function testClassDoubleShouldImplementTrait()
    {
        $double = Double::dummy(DoubleStandardClass::class)->addTrait(StandardTrait::class)->getInstance();
        $this->assertEquals(StandardTrait::class, class_uses($double)[StandardTrait::class]);
    }

    public function testAddInterfaceShouldAcceptArrayOfTraits()
    {
        $double = Double::dummy(DoubleStandardClass::class)
            ->addTrait([StandardTrait::class, StandardOtherTrait::class])
            ->getInstance();

        $this->assertEquals(StandardTrait::class, class_uses($double)[StandardTrait::class]);
        $this->assertEquals(StandardOtherTrait::class, class_uses($double)[StandardOtherTrait::class]);
    }

    public function testAddInterfaceWithInvalidClassShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        Double::dummy(DoubleStandardClass::class)->addInterface(new \stdClass());
    }

    public function testAddTraitWithInvalidClassShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        Double::dummy(DoubleStandardClass::class)->addTrait(new \stdClass());
    }

    public function testAddInterfaceWithInvalidMethodShouldFail()
    {
        $this->expectException(InvalidArgumentException::class);
        Double::dummy(DoubleStandardClass::class)->addInterface(IncompatibleInterface::class)->getClass();
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

    public function &referenceMethod(int $a)
    {
        return $a;
    }

    public function variadic(...$a)
    {
        return $a;
    }

    public function defaultWithOptional($default = null, $a = 1, $optional = null)
    {
        return $a;
    }

    public function defaultWithNullType(?string $a){
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

    public function returnNullType(): ?string
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

    final public static function bar()
    {
        return 'bar';
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

interface StandardOtherInterface
{
    public static function bar();
}

interface IncompatibleInterface
{
    static function foo();
}

trait StandardTrait
{
    function foo()
    {
        return 'foo';
    }
}

trait StandardOtherTrait
{
    function bar()
    {
        return 'bar';
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

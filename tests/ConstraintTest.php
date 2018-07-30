<?php
/**
 *
 * This file is part of the Doublit package.
 *
 * @license    MIT License
 * @link       https://github.com/gealex/doublit
 * @copyright  Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 *
 */

namespace Tests;


use Doublit\TestCase;
use Doublit\Constraints;
use PHPUnit\Framework\Constraint\ArrayHasKey;
use PHPUnit\Framework\Constraint\ArraySubset;
use PHPUnit\Framework\Constraint\Attribute;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\Constraint\ClassHasAttribute;
use PHPUnit\Framework\Constraint\ClassHasStaticAttribute;
use PHPUnit\Framework\Constraint\Count;
use PHPUnit\Framework\Constraint\DirectoryExists;
use PHPUnit\Framework\Constraint\FileExists;
use PHPUnit\Framework\Constraint\GreaterThan;
use PHPUnit\Framework\Constraint\IsAnything;
use PHPUnit\Framework\Constraint\IsEmpty;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsFalse;
use PHPUnit\Framework\Constraint\IsFinite;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\IsInfinite;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\Constraint\IsJson;
use PHPUnit\Framework\Constraint\IsNan;
use PHPUnit\Framework\Constraint\IsNull;
use PHPUnit\Framework\Constraint\IsReadable;
use PHPUnit\Framework\Constraint\IsTrue;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\Constraint\IsWritable;
use PHPUnit\Framework\Constraint\JsonMatches;
use PHPUnit\Framework\Constraint\LessThan;
use PHPUnit\Framework\Constraint\LogicalAnd;
use PHPUnit\Framework\Constraint\LogicalNot;
use PHPUnit\Framework\Constraint\LogicalOr;
use PHPUnit\Framework\Constraint\LogicalXor;
use PHPUnit\Framework\Constraint\Not;
use PHPUnit\Framework\Constraint\ObjectHasAttribute;
use PHPUnit\Framework\Constraint\RegularExpression;
use PHPUnit\Framework\Constraint\SameSize;
use PHPUnit\Framework\Constraint\StringContains;
use PHPUnit\Framework\Constraint\StringEndsWith;
use PHPUnit\Framework\Constraint\StringMatchesFormatDescription;
use PHPUnit\Framework\Constraint\StringStartsWith;
use PHPUnit\Framework\Constraint\TraversableContains;
use PHPUnit\Framework\Constraint\TraversableContainsOnly;


class ConstraintTest extends TestCase
{
    public function testLogicalAnd()
    {
        $constraint = Constraints::logicalAnd(Constraints::equalTo('3'), Constraints::greaterThan(4));
        $this->assertInstanceOf(LogicalAnd::class, $constraint);
    }

    public function testLogicalOr()
    {
        $constraint = Constraints::logicalOr(2,3);
        $this->assertInstanceOf(LogicalOr::class, $constraint);
    }

    public function testLogicalNot()
    {
        $constraint = Constraints::logicalNot(Constraints::equalTo('3'));
        $this->assertInstanceOf(LogicalNot::class, $constraint);
    }

    public function testLogicalXor()
    {
        $constraint = Constraints::logicalXor('2', '3');
        $this->assertInstanceOf(LogicalXor::class, $constraint);
    }

    public function testAnything()
    {
        $constraint = Constraints::anything();
        $this->assertInstanceOf(IsAnything::class, $constraint);
    }

    public function testIsTrue()
    {
        $constraint = Constraints::isTrue();
        $this->assertInstanceOf(IsTrue::class, $constraint);
    }

    public function testCallback()
    {
        $constraint = Constraints::callback(function () {
        });
        $this->assertInstanceOf(Callback::class, $constraint);
    }

    public function testIsFalse()
    {
        $constraint = Constraints::isFalse();
        $this->assertInstanceOf(IsFalse::class, $constraint);
    }


    public function testIsJson()
    {
        $constraint = Constraints::isJson();
        $this->assertInstanceOf(IsJson::class, $constraint);
    }

    public function testIsNull()
    {
        $constraint = Constraints::isNull();
        $this->assertInstanceOf(IsNull::class, $constraint);
    }

    public function testIsNotNull()
    {
        $constraint = Constraints::isNotNull();
        $this->assertInstanceOf(LogicalNot::class, $constraint);
    }

    public function testIsFinite()
    {
        $constraint = Constraints::isFinite();
        $this->assertInstanceOf(IsFinite::class, $constraint);
    }

    public function testIsInfinite()
    {
        $constraint = Constraints::isInfinite();
        $this->assertInstanceOf(IsInfinite::class, $constraint);
    }

    public function testIsNan()
    {
        $constraint = Constraints::isNan();
        $this->assertInstanceOf(IsNan::class, $constraint);
    }

    public function testAttribute()
    {
        $constraint = Constraints::attribute(Constraints::equalTo('1'), 'name');
        $this->assertInstanceOf(Attribute::class, $constraint);
    }

    public function testContains()
    {
        $constraint = Constraints::contains('value');
        $this->assertInstanceOf(TraversableContains::class, $constraint);
    }

    public function testContainsOnly()
    {
        $constraint = Constraints::containsOnly('string');
        $this->assertInstanceOf(TraversableContainsOnly::class, $constraint);
    }

    public function testContainsOnlyInstancesOf()
    {
        $constraint = Constraints::containsOnlyInstancesOf('Class');
        $this->assertInstanceOf(TraversableContainsOnly::class, $constraint);
    }

    public function testArrayHasKey()
    {
        $constraint = Constraints::arrayHasKey('key');
        $this->assertInstanceOf(ArrayHasKey::class, $constraint);
    }

    public function testEqualTo()
    {
        $constraint = Constraints::equalTo('3');
        $this->assertInstanceOf(IsEqual::class, $constraint);
    }

    public function testAttributeEqualTo()
    {
        $constraint = Constraints::attributeEqualTo('attribute', 'value');
        $this->assertInstanceOf(Attribute::class, $constraint);
    }

    public function testIsEmpty()
    {
        $constraint = Constraints::isEmpty();
        $this->assertInstanceOf(IsEmpty::class, $constraint);
    }

    public function testIsWritable()
    {
        $constraint = Constraints::isWritable();
        $this->assertInstanceOf(IsWritable::class, $constraint);
    }

    public function testIsReadable()
    {
        $constraint = Constraints::isReadable();
        $this->assertInstanceOf(IsReadable::class, $constraint);
    }

    public function testDirectoryExists()
    {
        $constraint = Constraints::directoryExists();
        $this->assertInstanceOf(DirectoryExists::class, $constraint);
    }

    public function testFileExists()
    {
        $constraint = Constraints::fileExists();
        $this->assertInstanceOf(FileExists::class, $constraint);
    }

    public function testGreaterThan()
    {
        $constraint = Constraints::greaterThan('3');
        $this->assertInstanceOf(GreaterThan::class, $constraint);
    }

    public function testGreaterThanOrEqual()
    {
        $constraint = Constraints::greaterThanOrEqual('3');
        $this->assertInstanceOf(LogicalOr::class, $constraint);
    }

    public function testClassHasAttribute()
    {
        $constraint = Constraints::classHasAttribute('attribute');
        $this->assertInstanceOf(ClassHasAttribute::class, $constraint);
    }

    public function testClassHasStaticAttribute()
    {
        $constraint = Constraints::classHasStaticAttribute('attribute');
        $this->assertInstanceOf(ClassHasStaticAttribute::class, $constraint);
    }

    public function testObjectHasAttribute()
    {
        $constraint = Constraints::objectHasAttribute('attribute');
        $this->assertInstanceOf(ObjectHasAttribute::class, $constraint);
    }

    public function testIdenticalTo()
    {
        $constraint = Constraints::identicalTo('value');
        $this->assertInstanceOf(IsIdentical::class, $constraint);
    }

    public function testIsInstanceOf()
    {
        $constraint = Constraints::isInstanceOf('Class');
        $this->assertInstanceOf(IsInstanceOf::class, $constraint);
    }

    public function testIsType()
    {
        $constraint = Constraints::isType('string');
        $this->assertInstanceOf(IsType::class, $constraint);
    }

    public function testLessThan()
    {
        $constraint = Constraints::lessThan('3');
        $this->assertInstanceOf(LessThan::class, $constraint);
    }

    public function testLessThanOrEqual()
    {
        $constraint = Constraints::lessThanOrEqual('3');
        $this->assertInstanceOf(LogicalOr::class, $constraint);
    }

    public function testMatchesRegularExpression()
    {
        $constraint = Constraints::matchesRegularExpression('##');
        $this->assertInstanceOf(RegularExpression::class, $constraint);
    }

    public function testMatches()
    {
        $constraint = Constraints::matches('string');
        $this->assertInstanceOf(StringMatchesFormatDescription::class, $constraint);
    }

    public function testStringStartsWith()
    {
        $constraint = Constraints::stringStartsWith('prefix');
        $this->assertInstanceOf(StringStartsWith::class, $constraint);
    }

    public function testStringContains()
    {
        $constraint = Constraints::stringContains('string');
        $this->assertInstanceOf(StringContains::class, $constraint);
    }

    public function testStringEndsWith()
    {
        $constraint = Constraints::stringEndsWith('suffix');
        $this->assertInstanceOf(StringEndsWith::class, $constraint);
    }

    public function testCountOf()
    {
        $constraint = Constraints::countOf('3');
        $this->assertInstanceOf(Count::class, $constraint);
    }

    public function testSameSize()
    {
        $constraint = Constraints::sameSize(new \ArrayIterator());
        $this->assertInstanceOf(SameSize::class, $constraint);
    }

    public function testArraySubset()
    {
        $constraint = Constraints::arraySubset(new \ArrayIterator());
        $this->assertInstanceOf(ArraySubset::class, $constraint);
    }

    public function testJsonMatch()
    {
        $constraint = Constraints::jsonMatches('{}');
        $this->assertInstanceOf(JsonMatches::class, $constraint);
    }
}

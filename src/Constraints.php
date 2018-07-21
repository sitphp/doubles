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

namespace Doublit;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\ArrayHasKey;
use PHPUnit\Framework\Constraint\ArraySubset;
use PHPUnit\Framework\Constraint\Attribute;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\Constraint\ClassHasAttribute;
use PHPUnit\Framework\Constraint\ClassHasStaticAttribute;
use PHPUnit\Framework\Constraint\Constraint;
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
use PHPUnit\Framework\Constraint\ObjectHasAttribute;
use PHPUnit\Framework\Constraint\RegularExpression;
use PHPUnit\Framework\Constraint\SameSize;
use PHPUnit\Framework\Constraint\StringContains;
use PHPUnit\Framework\Constraint\StringEndsWith;
use PHPUnit\Framework\Constraint\StringMatchesFormatDescription;
use PHPUnit\Framework\Constraint\StringStartsWith;
use PHPUnit\Framework\Constraint\TraversableContains;
use PHPUnit\Framework\Constraint\TraversableContainsOnly;

class Constraints
{

    /**
     * @param mixed ...$args
     * @return LogicalAnd
     */
    public static function logicalAnd(...$args)
    {
        return Assert::logicalAnd(...$args);
    }


    /**
     * @param mixed ...$args
     * @return LogicalOr
     */
    public static function logicalOr(...$args)
    {
        return Assert::logicalOr(...$args);
    }


    /**
     * @param Constraint $constraint
     * @return LogicalNot
     */
    public static function logicalNot(Constraint $constraint)
    {
        return Assert::logicalNot($constraint);
    }


    /**
     * @param mixed ...$args
     * @return LogicalXor
     */
    public static function logicalXor(...$args)
    {
        return Assert::logicalXor(...$args);
    }


    /**
     * @return IsAnything
     */
    public static function anything()
    {
        return Assert::anything();
    }


    /**
     * @return IsTrue
     */
    public static function isTrue()
    {
        return Assert::isTrue();
    }


    /**
     * @param $callback
     * @return Callback
     */
    public static function callback($callback)
    {
        return Assert::callback($callback);
    }


    /**
     * @return IsFalse
     */
    public static function isFalse()
    {
        return Assert::isFalse();
    }


    /**
     * @return IsJson
     */
    public static function isJson()
    {
        return Assert::isJson();
    }


    /**
     * @return IsNull
     */
    public static function isNull()
    {
        return Assert::isNull();
    }


    /**
     * @return LogicalNot
     */
    public static function isNotNull()
    {
        return Assert::logicalNot(Assert::isNull());
    }


    /**
     * @return IsFinite
     */
    public static function isFinite()
    {
        return Assert::isFinite();
    }


    /**
     * @return IsInfinite
     */
    public static function isInfinite()
    {
        return Assert::isInfinite();
    }


    /**
     * @return IsNan
     */
    public static function isNan()
    {
        return Assert::isNan();
    }


    /**
     * @param Constraint $constraint
     * @param $attributeName
     * @return Attribute
     */
    public static function attribute(Constraint $constraint, $attributeName)
    {
        return Assert::attribute($constraint, $attributeName);
    }


    /**
     * @param $value
     * @param bool $checkForObjectIdentity
     * @param bool $checkForNonObjectIdentity
     * @return TraversableContains
     */
    public static function contains($value, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false)
    {
        return Assert::contains($value, $checkForObjectIdentity, $checkForNonObjectIdentity);
    }


    /**
     * @param $type
     * @return TraversableContainsOnly
     */
    public static function containsOnly($type)
    {
        return Assert::containsOnly($type);
    }


    /**
     * @param $classname
     * @return TraversableContainsOnly
     */
    public static function containsOnlyInstancesOf($classname)
    {
        return Assert::containsOnlyInstancesOf($classname);
    }


    /**
     * @param $key
     * @return ArrayHasKey
     */
    public static function arrayHasKey($key)
    {
        return Assert::arrayHasKey($key);
    }


    /**
     * @param $subset
     * @param bool $strict
     * @return ArraySubset
     */
    public static function arraySubset($subset, $strict = false)
    {
        return new ArraySubset($subset, $strict);
    }


    /**
     * @param $value
     * @param float $delta
     * @param int $maxDepth
     * @param bool $canonicalize
     * @param bool $ignoreCase
     * @return IsEqual
     */
    public static function equalTo($value, $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        return Assert::equalTo($value, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }


    /**
     * @param $attributeName
     * @param $value
     * @param float $delta
     * @param int $maxDepth
     * @param bool $canonicalize
     * @param bool $ignoreCase
     * @return Attribute
     */
    public static function attributeEqualTo($attributeName, $value, $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)
    {
        return Assert::attributeEqualTo($attributeName, $value, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }


    /**
     * @return IsEmpty
     */
    public static function isEmpty()
    {
        return Assert::isEmpty();
    }


    /**
     * @return IsWritable
     */
    public static function isWritable()
    {
        return Assert::isWritable();
    }


    /**
     * @return IsReadable
     */
    public static function isReadable()
    {
        return Assert::isReadable();
    }


    /**
     * @return DirectoryExists
     */
    public static function directoryExists()
    {
        return Assert::directoryExists();
    }


    /**
     * @return FileExists
     */
    public static function fileExists()
    {
        return Assert::fileExists();
    }


    /**
     * @param $value
     * @return GreaterThan
     */
    public static function greaterThan($value)
    {
        return Assert::greaterThan($value);
    }


    /**
     * @param $value
     * @return LogicalOr
     */
    public static function greaterThanOrEqual($value)
    {
        return Assert::greaterThanOrEqual($value);
    }


    /**
     * @param $attributeName
     * @return ClassHasAttribute
     */
    public static function classHasAttribute($attributeName)
    {
        return Assert::classHasAttribute($attributeName);
    }


    /**
     * @param $attributeName
     * @return ClassHasStaticAttribute
     */
    public static function classHasStaticAttribute($attributeName)
    {
        return Assert::classHasStaticAttribute($attributeName);
    }


    /**
     * @param $attributeName
     * @return ObjectHasAttribute
     */
    public static function objectHasAttribute($attributeName)
    {
        return Assert::objectHasAttribute($attributeName);
    }


    /**
     * @param $value
     * @return IsIdentical
     */
    public static function identicalTo($value)
    {
        return Assert::identicalTo($value);
    }


    /**
     * @param $className
     * @return IsInstanceOf
     */
    public static function isInstanceOf($className)
    {
        return Assert::isInstanceOf($className);
    }


    /**
     * @param $type
     * @return IsType
     */
    public static function isType($type)
    {
        return Assert::isType($type);
    }


    /**
     * @param $value
     * @return LessThan
     */
    public static function lessThan($value)
    {
        return Assert::lessThan($value);
    }


    /**
     * @param $value
     * @return LogicalOr
     */
    public static function lessThanOrEqual($value)
    {
        return Assert::lessThanOrEqual($value);
    }


    /**
     * @param $pattern
     * @return RegularExpression
     */
    public static function matchesRegularExpression($pattern)
    {
        return Assert::matchesRegularExpression($pattern);
    }


    /**
     * @param $string
     * @return StringMatchesFormatDescription
     */
    public static function matches($string)
    {
        return Assert::matches($string);
    }


    /**
     * @param $expectedJson
     * @return JsonMatches
     */
    public static function jsonMatches($expectedJson)
    {
        return new JsonMatches($expectedJson);
    }


    /**
     * @param $expected
     * @return SameSize
     */
    public static function sameSize($expected)
    {
        return new SameSize($expected);
    }


    /**
     * @param $prefix
     * @return StringStartsWith
     */
    public static function stringStartsWith($prefix)
    {
        return Assert::stringStartsWith($prefix);
    }


    /**
     * @param $string
     * @param bool $case
     * @return StringContains
     */
    public static function stringContains($string, $case = true)
    {
        return Assert::stringContains($string, $case);
    }


    /**
     * @param $suffix
     * @return StringEndsWith
     */
    public static function stringEndsWith($suffix)
    {
        return Assert::stringEndsWith($suffix);
    }


    /**
     * @param $count
     * @return Count
     */
    public static function countOf($count)
    {
        return Assert::countOf($count);
    }
}

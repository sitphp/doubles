<?php
/**
 * This file is part of the "sitphp/doubles" package.
 *
 * @license MIT License
 * @link https://github.com/sitphp/doubles
 * @copyright Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 */

namespace SitPHP\Doubles;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\ArrayHasKey;
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
use PHPUnit\Framework\Constraint\StringContains;
use PHPUnit\Framework\Constraint\StringEndsWith;
use PHPUnit\Framework\Constraint\StringStartsWith;
use PHPUnit\Framework\Constraint\TraversableContains;
use PHPUnit\Framework\Constraint\TraversableContainsEqual;
use PHPUnit\Framework\Constraint\TraversableContainsIdentical;
use PHPUnit\Framework\Constraint\TraversableContainsOnly;

class Constraints
{

    /**
     * @param mixed ...$args
     * @return LogicalAnd
     */
    public static function logicalAnd(...$args): LogicalAnd
    {
        return Assert::logicalAnd(...$args);
    }


    /**
     * @param mixed ...$args
     * @return LogicalOr
     */
    public static function logicalOr(...$args): LogicalOr
    {
        return Assert::logicalOr(...$args);
    }


    /**
     * @param Constraint $constraint
     * @return LogicalNot
     */
    public static function logicalNot(Constraint $constraint): LogicalNot
    {
        return Assert::logicalNot($constraint);
    }


    /**
     * @param mixed ...$args
     * @return LogicalXor
     */
    public static function logicalXor(...$args): LogicalXor
    {
        return Assert::logicalXor(...$args);
    }


    /**
     * @return IsAnything
     */
    public static function anything(): IsAnything
    {
        return Assert::anything();
    }


    /**
     * @return IsTrue
     */
    public static function isTrue(): IsTrue
    {
        return Assert::isTrue();
    }


    /**
     * @param $callback
     * @return Callback
     */
    public static function callback($callback): Callback
    {
        return Assert::callback($callback);
    }


    /**
     * @return IsFalse
     */
    public static function isFalse(): IsFalse
    {
        return Assert::isFalse();
    }


    /**
     * @return IsJson
     */
    public static function isJson(): IsJson
    {
        return Assert::isJson();
    }


    /**
     * @return IsNull
     */
    public static function isNull(): IsNull
    {
        return Assert::isNull();
    }


    /**
     * @return LogicalNot
     */
    public static function isNotNull(): LogicalNot
    {
        return Assert::logicalNot(Assert::isNull());
    }


    /**
     * @return IsFinite
     */
    public static function isFinite(): IsFinite
    {
        return Assert::isFinite();
    }


    /**
     * @return IsInfinite
     */
    public static function isInfinite(): IsInfinite
    {
        return Assert::isInfinite();
    }


    /**
     * @return IsNan
     */
    public static function isNan(): IsNan
    {
        return Assert::isNan();
    }


    /**
     * @param $value
     * @param bool $checkForObjectIdentity
     * @param bool $checkForNonObjectIdentity
     * @return TraversableContains
     * @deprecated Use containsEqual() or containsIdentical() instead
     */
    public static function contains($value, bool $checkForObjectIdentity = true, bool $checkForNonObjectIdentity = false): TraversableContains
    {
        return Assert::contains($value, $checkForObjectIdentity, $checkForNonObjectIdentity);
    }

    /**
     * @param $value
     * @return TraversableContainsEqual
     */
    public static function containsEqual($value): TraversableContainsEqual
    {
        return Assert::containsEqual($value);
    }

    /**
     * @param $value
     * @return TraversableContainsIdentical
     */
    public static function containsIdentical($value): TraversableContainsIdentical
    {
        return Assert::containsIdentical($value);
    }


    /**
     * @param $type
     * @return TraversableContainsOnly
     */
    public static function containsOnly($type): TraversableContainsOnly
    {
        return Assert::containsOnly($type);
    }


    /**
     * @param $classname
     * @return TraversableContainsOnly
     */
    public static function containsOnlyInstancesOf($classname): TraversableContainsOnly
    {
        return Assert::containsOnlyInstancesOf($classname);
    }


    /**
     * @param $key
     * @return ArrayHasKey
     */
    public static function arrayHasKey($key): ArrayHasKey
    {
        return Assert::arrayHasKey($key);
    }


    /**
     * @param $value
     * @param float $delta
     * @param int $maxDepth
     * @param bool $canonicalize
     * @param bool $ignoreCase
     * @return IsEqual
     */
    public static function equalTo($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = false): IsEqual
    {
        return Assert::equalTo($value, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }


    /**
     * @return IsEmpty
     */
    public static function isEmpty(): IsEmpty
    {
        return Assert::isEmpty();
    }


    /**
     * @return IsWritable
     */
    public static function isWritable(): IsWritable
    {
        return Assert::isWritable();
    }


    /**
     * @return IsReadable
     */
    public static function isReadable(): IsReadable
    {
        return Assert::isReadable();
    }


    /**
     * @return DirectoryExists
     */
    public static function directoryExists(): DirectoryExists
    {
        return Assert::directoryExists();
    }


    /**
     * @return FileExists
     */
    public static function fileExists(): FileExists
    {
        return Assert::fileExists();
    }


    /**
     * @param $value
     * @return GreaterThan
     */
    public static function greaterThan($value): GreaterThan
    {
        return Assert::greaterThan($value);
    }


    /**
     * @param $value
     * @return LogicalOr
     */
    public static function greaterThanOrEqual($value): LogicalOr
    {
        return Assert::greaterThanOrEqual($value);
    }


    /**
     * @param $attributeName
     * @return ClassHasAttribute
     */
    public static function classHasAttribute($attributeName): ClassHasAttribute
    {
        return Assert::classHasAttribute($attributeName);
    }


    /**
     * @param $attributeName
     * @return ClassHasStaticAttribute
     */
    public static function classHasStaticAttribute($attributeName): ClassHasStaticAttribute
    {
        return Assert::classHasStaticAttribute($attributeName);
    }


    /**
     * @param $attributeName
     * @return ObjectHasAttribute
     */
    public static function objectHasAttribute($attributeName): ObjectHasAttribute
    {
        return Assert::objectHasAttribute($attributeName);
    }


    /**
     * @param $value
     * @return IsIdentical
     */
    public static function identicalTo($value): IsIdentical
    {
        return Assert::identicalTo($value);
    }


    /**
     * @param $className
     * @return IsInstanceOf
     */
    public static function isInstanceOf($className): IsInstanceOf
    {
        return Assert::isInstanceOf($className);
    }


    /**
     * @param $type
     * @return IsType
     */
    public static function isType($type): IsType
    {
        return Assert::isType($type);
    }

    /**
     * Asserts that a variable is of type array.
     */
    public static function isArray(): IsType
    {
        return Assert::isType(IsType::TYPE_ARRAY);
    }

    /**
     * Asserts that a variable is of type bool.
     */
    public static function isBool(): IsType
    {
        return Assert::isType(IsType::TYPE_BOOL);
    }

    /**
     * Asserts that a variable is of type float.
     */
    public static function isFloat(): IsType
    {
        return Assert::isType(IsType::TYPE_FLOAT);
    }

    /**
     * Asserts that a variable is of type int.
     */
    public static function isInt(): IsType
    {
        return Assert::isType(IsType::TYPE_INT);
    }

    /**
     * Asserts that a variable is of type numeric.
     */
    public static function isNumeric(): IsType
    {
        return Assert::isType(IsType::TYPE_NUMERIC);
    }

    /**
     * Asserts that a variable is of type object.
     */
    public static function isObject(): IsType
    {
        return Assert::isType(IsType::TYPE_OBJECT);
    }

    /**
     * Asserts that a variable is of type resource.
     */
    public static function isResource(): IsType
    {
        return Assert::isType(IsType::TYPE_RESOURCE);
    }

    /**
     * Asserts that a variable is of type string.
     */
    public static function isString(): IsType
    {
        return Assert::isType(IsType::TYPE_STRING);
    }

    /**
     * Asserts that a variable is of type scalar.
     */
    public static function isScalar(): IsType
    {
        return Assert::isType(IsType::TYPE_SCALAR);
    }

    /**
     * Asserts that a variable is of type callable.
     */
    public static function isCallable(): IsType
    {
        return Assert::isType(IsType::TYPE_CALLABLE);
    }

    /**
     * Asserts that a variable is of type iterable.
     */
    public static function isIterable(): IsType
    {
        return Assert::isType(IsType::TYPE_ITERABLE);
    }

    /**
     * Asserts that a variable is of type array.
     */
    public static function isNotArray(): LogicalNot
    {
        return self::logicalNot(Assert::isType(IsType::TYPE_ARRAY));
    }

    /**
     * Asserts that a variable is of type bool.
     */
    public static function isNotBool(): LogicalNot
    {
        return self::logicalNot(Assert::isType(IsType::TYPE_BOOL));
    }

    /**
     * Asserts that a variable is of type float.
     */
    public static function isNotFloat(): LogicalNot
    {
        return self::logicalNot(Assert::isType(IsType::TYPE_FLOAT));
    }

    /**
     * Asserts that a variable is of type int.
     */
    public static function isNotInt(): LogicalNot
    {
        return self::logicalNot(Assert::isType(IsType::TYPE_INT));
    }

    /**
     * Asserts that a variable is of type numeric.
     */
    public static function isNotNumeric(): LogicalNot
    {
        return self::logicalNot(Assert::isType(IsType::TYPE_NUMERIC));
    }

    /**
     * Asserts that a variable is of type object.
     */
    public static function isNotObject(): LogicalNot
    {
        return self::logicalNot(Assert::isType(IsType::TYPE_OBJECT));
    }

    /**
     * Asserts that a variable is of type resource.
     */
    public static function isNotResource(): LogicalNot
    {
        return self::logicalNot(Assert::isType(IsType::TYPE_RESOURCE));
    }

    /**
     * Asserts that a variable is of type string.
     */
    public static function isNotString(): LogicalNot
    {
        return self::logicalNot(Assert::isType(IsType::TYPE_STRING));
    }

    /**
     * Asserts that a variable is of type scalar.
     */
    public static function isNotScalar(): LogicalNot
    {
        return self::logicalNot(Assert::isType(IsType::TYPE_SCALAR));
    }

    /**
     * Asserts that a variable is of type callable.
     */
    public static function isNotCallable(): LogicalNot
    {
        return self::logicalNot(Assert::isType(IsType::TYPE_CALLABLE));
    }

    /**
     * Asserts that a variable is of type iterable.
     */
    public static function isNotIterable(): LogicalNot
    {
        return self::logicalNot(Assert::isType(IsType::TYPE_ITERABLE));
    }


    /**
     * @param $value
     * @return LessThan
     */
    public static function lessThan($value): LessThan
    {
        return Assert::lessThan($value);
    }


    /**
     * @param $value
     * @return LogicalOr
     */
    public static function lessThanOrEqual($value): LogicalOr
    {
        return Assert::lessThanOrEqual($value);
    }


    /**
     * @param $pattern
     * @return RegularExpression
     */
    public static function matchesRegularExpression($pattern): RegularExpression
    {
        return Assert::matchesRegularExpression($pattern);
    }


    /**
     * @param $expectedJson
     * @return JsonMatches
     */
    public static function jsonMatches($expectedJson): JsonMatches
    {
        return new JsonMatches($expectedJson);
    }


    /**
     * @param $prefix
     * @return StringStartsWith
     */
    public static function stringStartsWith($prefix): StringStartsWith
    {
        return Assert::stringStartsWith($prefix);
    }


    /**
     * @param $string
     * @param bool $case
     * @return StringContains
     */
    public static function stringContains($string, bool $case = true): StringContains
    {
        return Assert::stringContains($string, $case);
    }


    /**
     * @param $suffix
     * @return StringEndsWith
     */
    public static function stringEndsWith($suffix): StringEndsWith
    {
        return Assert::stringEndsWith($suffix);
    }


    /**
     * @param $count
     * @return Count
     */
    public static function countOf($count): Count
    {
        return Assert::countOf($count);
    }
}

<?php
/**
 * This file is part of the "sitphp/doubles" package.
 *
 * @license MIT License
 * @link https://github.com/sitphp/doubles
 * @copyright Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 */

namespace SitPHP\Doubles;

use SitPHP\Doubles\Stubs\ReturnArgumentStub;
use SitPHP\Doubles\Stubs\ReturnCallbackStub;
use SitPHP\Doubles\Stubs\ReturnSelfStub;
use SitPHP\Doubles\Stubs\ReturnValueMapStub;
use SitPHP\Doubles\Stubs\ReturnValueStub;
use SitPHP\Doubles\Stubs\ThrowExceptionStub;

class Stubs
{
    /**
     * @param mixed $value
     * @return ReturnValueStub
     */
    public static function returnValue($value): ReturnValueStub
    {
        return new ReturnValueStub($value);
    }

    /**
     * @param array $args_map
     * @param $returns_map
     * @return ReturnValueMapStub
     */
    public static function returnValueMap(array $args_map, $returns_map): ReturnValueMapStub
    {
        return new ReturnValueMapStub($args_map, $returns_map);
    }

    /**
     * @param $arg_index
     * @return ReturnArgumentStub
     */
    public static function returnArgument($arg_index): ReturnArgumentStub
    {
        return new ReturnArgumentStub($arg_index);
    }

    /**
     * @param mixed $callback
     *
     * @return ReturnCallbackStub
     */
    public static function returnCallback($callback): ReturnCallbackStub
    {
        return new ReturnCallbackStub($callback);
    }

    /**
     * @return ReturnSelfStub
     */
    public static function returnSelf(): ReturnSelfStub
    {
        return new ReturnSelfStub();
    }

    /**
     * @param $exception_class
     * @param null $message
     * @return ThrowExceptionStub
     */
    public static function throwException($exception_class, $message = null): ThrowExceptionStub
    {
        return new ThrowExceptionStub($exception_class, $message);
    }
}

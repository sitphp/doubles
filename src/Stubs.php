<?php
/**
 * This file is part of the "sitphp/doubles" package.
 *
 *  @license MIT License
 *  @link https://github.com/sitphp/doubles
 *  @copyright Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 */

namespace Doubles;

use Doubles\Stubs\ReturnArgumentStub;
use Doubles\Stubs\ReturnCallbackStub;
use Doubles\Stubs\ReturnSelfStub;
use Doubles\Stubs\ReturnValueMapStub;
use Doubles\Stubs\ReturnValueStub;
use Doubles\Stubs\ThrowExceptionStub;

class Stubs
{
    /**
     * @param mixed $value
     * @return ReturnValueStub
     */
    public static function returnValue($value)
    {
        return new ReturnValueStub($value);
    }

    /**
     * @param array $args_map
     * @param array $returns_map
     * @return ReturnValueMapStub
     */
    public static function returnValueMap(array $args_map, array $returns_map)
    {
        return new ReturnValueMapStub($args_map, $returns_map);
    }

    /**
     * @param $arg_index
     * @return ReturnArgumentStub
     */
    public static function returnArgument($arg_index)
    {
        return new ReturnArgumentStub($arg_index);
    }

    /**
     * @param mixed $callback
     *
     * @return ReturnCallbackStub
     */
    public static function returnCallback($callback)
    {
        return new ReturnCallbackStub($callback);
    }

    /**
     * @return ReturnSelfStub
     */
    public static function returnSelf()
    {
        return new ReturnSelfStub();
    }

    /**
     * @param $exception_class
     * @param null $message
     * @return ThrowExceptionStub
     */
    public static function throwException($exception_class, $message = null)
    {
        return new ThrowExceptionStub($exception_class, $message);
    }
}

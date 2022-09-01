<?php
/**
 * This file is part of the "sitphp/doubles" package.
 *
 *  @license MIT License
 *  @link https://github.com/sitphp/doubles
 *  @copyright Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 */

namespace Doubles\Stubs;


use Closure;
use Doubles\Exceptions\InvalidArgumentException;

class ReturnCallbackStub implements StubInterface
{
    protected $callback;

    function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Invalid callback');
        }
        $this->callback = $callback;
    }

    function invoke(array $call)
    {
        $callback = $this->callback;
        if ($callback instanceof Closure) {
            return $callback(...$call['args']);
        }
        return call_user_func_array($callback, $call['args']);
    }
}

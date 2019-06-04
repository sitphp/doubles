<?php
/**
 * This file is part of the "sitphp/doubles" package.
 *
 *  @license MIT License
 *  @link https://github.com/sitphp/doubles
 *  @copyright Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 */

namespace Doubles\Stubs;

class ReturnValueStub implements StubInterface
{
    protected $value;

    function __construct($value)
    {
        $this->value = $value;
    }

    function invoke(array $call)
    {
        return $this->value;
    }
}

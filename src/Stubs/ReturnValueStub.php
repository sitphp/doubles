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

namespace Doublit\Stubs;

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

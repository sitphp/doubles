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

class ThrowExceptionStub implements StubInterface
{
    protected $exception_class;
    protected $message;

    function __construct($exception_class, $message = null)
    {
        $this->exception_class = $exception_class;
        if (isset($message)) {
            $this->message = $message;
        }
    }

    function invoke(array $call)
    {
        if (isset($this->message)) {
            throw new $this->exception_class($this->message);
        }
        throw new $this->exception_class();
    }
}

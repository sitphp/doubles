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

use Doublit\Exceptions\InvalidArgumentException;

class ReturnArgumentStub implements StubInterface
{
    protected $argument_index;

    function __construct($argument_index)
    {
        if (filter_var($argument_index, FILTER_VALIDATE_INT) === 0 || !filter_var($argument_index, FILTER_VALIDATE_INT)) {
            throw new InvalidArgumentException('Argument index should be an integer');
        }
        $this->argument_index = $argument_index;
    }

    function invoke(array $call)
    {
        $args = $call['args'];
        if (!isset($args[$this->argument_index - 1])) {
            throw new InvalidArgumentException('Argument ' . $this->argument_index . ' was not defined and therefore cannot be returned');
        }
        return $args[$this->argument_index - 1];
    }
}

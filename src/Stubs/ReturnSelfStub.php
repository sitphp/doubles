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


use Doublit\Exceptions\RuntimeException;

class ReturnSelfStub implements StubInterface
{
    function invoke(array $call)
    {
        if (isset($call['instance'])) {
            return $call['instance'];
        } else if (isset($call['class'])) {
            return $call['class'];
        } else {
            throw new RuntimeException('Call instance and class not found');
        }
    }
}

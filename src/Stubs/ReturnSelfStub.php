<?php
/**
 * This file is part of the "sitphp/doubles" package.
 *
 * @license MIT License
 * @link https://github.com/sitphp/doubles
 * @copyright Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 */

namespace SitPHP\Doubles\Stubs;


use SitPHP\Doubles\Exceptions\RuntimeException;

class ReturnSelfStub implements StubInterface
{
    function invoke(array $call)
    {
        if (isset($call['instance'])) {
            return $call['instance'];
        } else if (isset($call['class'])) {
            return $call['class'];
        } else {
            throw new RuntimeException('Call instance/class not found');
        }
    }
}

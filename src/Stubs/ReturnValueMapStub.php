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

class ReturnValueMapStub implements StubInterface
{
    protected $args_map;
    protected $returns_map;

    function __construct(array $args_map, array $returns_map)
    {
        if (count($args_map) != count($returns_map)) {
            throw new InvalidArgumentException('Argument count does not match return count');
        }
        $this->args_map = $args_map;
        $this->returns_map = $returns_map;
    }

    function invoke(array $call)
    {
        $args = $call['args'];
        foreach ($this->args_map as $i => $arg_map) {
            if ($args == $arg_map) {
                return $this->returns_map[$i];
            }
        }
    }

}

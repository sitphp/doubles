<?php
/**
 * This file is part of the "sitphp/doubles" package.
 *
 *  @license MIT License
 *  @link https://github.com/sitphp/doubles
 *  @copyright Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 */

namespace Doubles\Stubs;

use Doubles\Constraints;
use Doubles\Exceptions\InvalidArgumentException;
use Doubles\Exceptions\RuntimeException;
use Doubles\Lib\DoubleStub;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\Constraint;

class ReturnValueMapStub implements StubInterface
{
    protected $args_map;
    protected $returns_map;

    function __construct($args_map, $returns_map)
    {
        if(!is_array($args_map)){
            $args_map = [$args_map];
        }
        if(!is_array($returns_map)){
            $returns_map = [$returns_map];
        }

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
            if ($this->argMapMatchesArgs($arg_map, $args)) {
                return $this->returns_map[$i];
            }
        }

        $method = $call['method'];
        if (isset($call['instance'])) {
            /** @var DoubleStub $instance */
            $instance = $call['instance'];
            $instance::_method($method)->default();
            return $instance->$method(...$args);
        } else if (isset($call['class'])) {
            /** @var DoubleStub $class */
            $class = $call['class'];
            $class::_method($method)->default();
            return $class::$method(...$args);
        }
    }

    protected function argMapMatchesArgs(array $arg_map, array $args){
        foreach ($arg_map as $i => $arg){
            if ($arg instanceof Constraint) {
                $constraint = $arg;
            } else if (is_bool($arg)) {
                $constraint = $arg ? Constraints::isTrue() : Constraints::isFalse();
            } else if ($arg === null) {
                $constraint = Constraints::isNull();
            } else {
                $constraint = Constraints::equalTo($arg);
            }
            try{
                $constraint->evaluate($args[$i]);
            } catch (\Exception $e){
                return false;
            }
        }
        return true;
    }

}

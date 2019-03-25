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

namespace Doublit\Lib;

use \Doublit\Stubs;
use \Doublit\Constraints;
use \Doublit\Stubs\StubInterface;
use PHPUnit\Framework\Constraint\Constraint;
use \Doublit\Exceptions\InvalidArgumentException;

class Expectation
{
    protected $method;
    protected $type = [];
    protected $args = [];
    protected $count;


    /**
     * Expectation constructor.
     *
     * @param string $method
     */
    function __construct(string $method)
    {
        $this->method = $method;
    }

    /**
     * Make method a dummy for a specific call
     * or for all calls if $call_count is null
     *
     * @param null $call_number
     * @return Expectation
     */
    function dummy($call_number = null)
    {
        if (isset($call_number) && is_array($call_number)) {
            foreach ($call_number as $value) {
                $this->dummy($value);
            }
            return $this;
        }
        $this->validateCallCount($call_number);
        if (isset($call_number)) {
            $this->setTypeCall($call_number, ['dummy']);
        } else {
            $this->resetType();
            $this->setTypeDefault(['dummy']);
        }
        return $this;
    }

    /**
     * Make method a mock for a specific call
     * or for all calls if $call_count is null
     *
     * @param null $call_number
     * @return Expectation
     */
    function mock($call_number = null)
    {
        if (isset($call_number) && is_array($call_number)) {
            foreach ($call_number as $value) {
                $this->mock($value);
            }
            return $this;
        }
        $this->validateCallCount($call_number);
        if (isset($call_number)) {
            $this->setTypeCall($call_number, ['mock']);
        } else {
            $this->resetType();
            $this->setTypeDefault(['mock']);
        }
        return $this;
    }

    /**
     * Make method a stub that runs a specific callback
     * for a specific call or for all calls if $call_count is null
     *
     * @param $return
     * @param null $call_number
     * @return Expectation
     */
    function stub($return, $call_number = null)
    {
        $this->validateCallCount($call_number);
        if (is_callable($return)) {
            $stub = Stubs::returnCallback($return);
        } else if ($return instanceof StubInterface) {
            $stub = $return;
        } else {
            $stub = Stubs::returnValue($return);
        }
        if (isset($call_number)) {
            if (is_array($call_number)) {
                foreach ($call_number as $value) {
                    $this->setTypeCall($value, ['stub', $stub]);
                }
            } else {
                $this->setTypeCall($call_number, ['stub', $stub]);
            }
        } else {
            $this->resetType();
            $this->setTypeDefault(['stub', $stub]);
        }
        return $this;
    }

    /**
     * Set method count constraint
     *
     * @param $count
     * @return $this
     * @throws \Exception
     */
    function count($count)
    {
        if ($this->isInt($count)) {
            if ($count < 0) {
                throw new InvalidArgumentException('Invalid "range" argument. Should be "i", ">i", "<i" ">=i", "<=i" (where i is a positive integer), callable or instance of ' . Constraint::class);
            }
        } else if (is_string($count)) {
            if ($count[0] == '>') {
                if ($count[1] === '=') {
                    $limit = ltrim($count, '>=');
                } else {
                    $limit = ltrim($count, '>');
                }
                if (!$this->isPositiveInt($limit)) {
                    throw new InvalidArgumentException('Invalid "range" argument. Should be "i", ">i", "<i" ">=i", "<=i" (where i is a positive integer), callable or instance of ' . Constraint::class);
                }
            } else if ($count[0] == '<') {
                if ($count[1] == '=') {
                    $limit = ltrim($count, '<=');
                } else {
                    $limit = ltrim($count, '<');
                }
                if (!$this->isPositiveInt($limit)) {
                    throw new InvalidArgumentException('Invalid "range" argument. Should be "i", ">i", "<i" ">=i", "<=i" (where i is a positive integer), callable or instance of ' . Constraint::class);
                }

            } else if (strpos('-', $count)) {
                $limits = explode('-', $count);
                if ($count($limits) != 2) {
                    throw new InvalidArgumentException('Invalid "range" argument. Should be "i", ">i", "<i" ">=i", "<=i" (where i is a positive integer), callable or instance of ' . Constraint::class);
                }
                foreach ($limits as $limit) {
                    if (!$this->isPositiveInt($limit)) {
                        throw new InvalidArgumentException('Invalid "range" argument. Should be "i", ">i", "<i" ">=i", "<=i" (where i is a positive integer), callable or instance of ' . Constraint::class);
                    }
                }
            }
        } else if (!$count instanceof Constraint && !is_callable($count)) {
            throw new InvalidArgumentException('Invalid "range" argument. Should be "i", ">i", "<i" ">=i", "<=i" (where i is a positive integer), callable or instance of ' . Constraint::class);
        }
        $this->count = $count;
        return $this;
    }

    /**
     * Set arguments constraint
     *
     * @param $arguments_assertions
     * @param null $call_number
     * @return Expectation
     */
    function args($arguments_assertions, $call_number = null)
    {
        $this->validateCallCount($call_number);
        if (is_array($arguments_assertions)) {
            foreach ($arguments_assertions as $key => $argument_assertion) {
                if (is_bool($argument_assertion)) {
                    if ($argument_assertion) {
                        $arguments_assertions[$key] = Constraints::isTrue();
                    } else {
                        $arguments_assertions[$key] = Constraints::isFalse();
                    }
                } else if ($argument_assertion === null) {
                    $arguments_assertions[$key] = Constraints::isNull();
                } else if ($argument_assertion instanceof Constraint) {
                    continue;
                } else {
                    $arguments_assertions[$key] = Constraints::equalTo($argument_assertion);
                }
            }
        } else if ($arguments_assertions === null) {
            $arguments_assertions = 'no-args';
        } else if (!$arguments_assertions instanceof \Closure) {
            throw new InvalidArgumentException('Invalid "arguments_assertions" argument. Should be array, null or callback');
        }

        if (isset($call_number)) {
            if (is_array($call_number)) {
                foreach ($call_number as $value) {
                    $this->setArgsCall($value, $arguments_assertions);
                }
            } else {
                $this->setArgsCall($call_number, $arguments_assertions);
            }
        } else {
            $this->resetArgs();
            $this->setArgsDefault($arguments_assertions);
        }
        return $this;
    }

    /**
     * Get count constraint
     *
     * @return mixed
     */
    function getCount()
    {
        return $this->count;
    }

    /**
     * Validate call count value
     *
     * @param $call_count
     */
    protected function validateCallCount($call_count)
    {
        if (is_array($call_count)) {
            foreach ($call_count as $value) {
                $this->validateCallCount($value);
            }
            return;
        }
        if ($call_count !== null && (!$this->isInt($call_count) || $call_count < 1)) {
            throw new InvalidArgumentException('Argument "call_number" should be a positive int');
        }
    }


    /**
     * Return method type on specific call
     *
     * @param $call_number
     * @return mixed|null
     */
    function getType($call_number)
    {
        if (isset($this->type[$call_number])) {
            return $this->type[$call_number];
        } else if (isset($this->type['_default'])) {
            return $this->type['_default'];
        } else {
            return null;
        }
    }

    /**
     * Reset type assertion array
     */
    protected function resetType()
    {
        $this->type = [];
    }

    /**
     * Set method default type
     *
     * @param $type
     */
    protected function setTypeDefault(array $type)
    {
        $this->type['_default'] = $type;
    }

    /**
     * Set method type on specific call
     *
     * @param $call_number
     * @param array $type
     */
    protected function setTypeCall($call_number, array $type)
    {
        $this->type[$call_number] = $type;
    }

    /**
     * Return argument constraint or null
     * if no constraint is defined
     *
     * @param $call_number
     * @return callable|null
     */
    function getArgs($call_number)
    {
        if (!$this->isInt($call_number) || $call_number < 1) {
            throw new InvalidArgumentException('Argument "call_number" should be a positive int');
        }
        if (isset($this->args[$call_number])) {
            return $this->args[$call_number];
        } else if (isset($this->args['_default'])) {
            return $this->args['_default'];
        } else {
            return null;
        }
    }

    /**
     * Reset arguments assertions array
     */
    protected function resetArgs()
    {
        $this->args = [];
    }

    /**
     * Set default arguments constraints
     *
     * @param $constraints
     */
    protected function setArgsDefault($constraints)
    {
        $this->args['_default'] = $constraints;
    }

    /**
     * Set arguments constraints on specific call
     *
     * @param $call_number
     * @param $constraints
     */
    protected function setArgsCall($call_number, $constraints)
    {
        $this->args[$call_number] = $constraints;
    }

    /**
     * Check if value is a positive integer
     *
     * @param $int
     * @return bool
     */
    protected function isPositiveInt($int)
    {
        return $this->isInt($int) && $int >= 0;
    }

    /**
     * Check if value is an integer
     *
     * @param $int
     * @return bool
     */
    protected function isInt($int)
    {
        return filter_var($int, FILTER_VALIDATE_INT) === 0 || filter_var($int, FILTER_VALIDATE_INT);
    }
}
<?php
/**
 * This file is part of the "sitphp/doubles" package.
 *
 * @license MIT License
 * @link https://github.com/sitphp/doubles
 * @copyright Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 */

namespace SitPHP\Doubles\Lib;

use SitPHP\Doubles\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\Constraint\Constraint;
use SitPHP\Doubles\Constraints;
use SitPHP\Doubles\Stubs;
use SitPHP\Doubles\Stubs\StubInterface;

class Expectation
{
    const TYPE_STUB = 'stub';
    const TYPE_DUMMY = 'dummy';
    const TYPE_MOCK = 'mock';

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
        $call_counts = $this->validateCallCounts($call_number);
        if ($call_counts !== null) {
            foreach ($call_counts as $value) {
                $this->setTypeCall($value, [self::TYPE_DUMMY]);
            }
        } else {
            $this->resetType();
            $this->setTypeDefault([self::TYPE_DUMMY]);
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
        $call_counts = $this->validateCallCounts($call_number);
        if ($call_counts !== null) {
            foreach ($call_counts as $value) {
                $this->setTypeCall($value, [self::TYPE_MOCK]);
            }
        } else {
            $this->resetType();
            $this->setTypeDefault([self::TYPE_MOCK]);
        }
        return $this;
    }

    function default($call_number = null)
    {
        $call_counts = $this->validateCallCounts($call_number);
        if ($call_counts !== null) {
            foreach ($call_counts as $value) {
                $this->resetTypeCall($value);
            }
        } else {
            $this->resetType();
        }
    }

    /**
     * Make method a stub that runs a specific callback
     * for a specific call or for all calls if $call_count is null
     *
     * @param $return
     * @param null $call_number
     * @return Expectation
     */
    function return($return, $call_number = null)
    {
        $call_counts = $this->validateCallCounts($call_number);
        if (is_callable($return)) {
            $stub = Stubs::returnCallback($return);
        } else if ($return instanceof StubInterface) {
            $stub = $return;
        } else {
            $stub = Stubs::returnValue($return);
        }

        if ($call_counts !== null) {
            foreach ($call_counts as $call_count) {
                $this->setTypeCall($call_count, [self::TYPE_STUB, $stub]);
            }
        } else {
            $this->resetType();
            $this->setTypeDefault([self::TYPE_STUB, $stub]);
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
                $this->throwInvalidRangeException();
            }
        } else if (is_string($count)) {
            if ($count[0] == '>') {
                if ($count[1] === '=') {
                    $limit = ltrim($count, '>=');
                } else {
                    $limit = ltrim($count, '>');
                }
                if (!$this->isPositiveInt($limit)) {
                    $this->throwInvalidRangeException();
                }
            } else if ($count[0] == '<') {
                if ($count[1] == '=') {
                    $limit = ltrim($count, '<=');
                } else {
                    $limit = ltrim($count, '<');
                }
                if (!$this->isPositiveInt($limit)) {
                    $this->throwInvalidRangeException();
                }

            } else if (strpos($count, '-') !== false) {
                $limits = explode('-', $count);
                if (count($limits) != 2) {
                    $this->throwInvalidRangeException();
                }
                foreach ($limits as $limit) {
                    if (!$this->isPositiveInt($limit)) {
                        $this->throwInvalidRangeException();
                    }
                }
            } else {
                $this->throwInvalidRangeException();
            }
        } else if (!$count instanceof Constraint && !is_callable($count)) {
            $this->throwInvalidRangeException();
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
        $call_counts = $this->validateCallCounts($call_number);
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

        if ($call_counts !== null) {
            foreach ($call_counts as $value) {
                $this->setArgsCall($value, $arguments_assertions);
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
     * @param $call_counts
     * @return null
     */
    protected function validateCallCounts($call_counts)
    {
        if ($call_counts === null) {
            return null;
        }
        if (!is_array($call_counts)) {
            $call_counts = [$call_counts];
        }
        foreach ($call_counts as $value) {
            if (!$this->isPositiveInt($value) || $value < 1) {
                throw new InvalidArgumentException('Argument "call_number" should be a positive int');
            }
        }
        return $call_counts;
    }


    /**
     * Return method type on specific call
     *
     * @param $call_number
     * @return mixed|null
     */
    function getType($call_number)
    {
        if (array_key_exists($call_number, $this->type)) {
            return $this->type[$call_number];
        }
        return $this->type['_default'] ?? null;
    }

    /**
     * Reset type assertion array
     */
    protected function resetType()
    {
        $this->type = [];
    }

    protected function resetTypeCall(int $call_number)
    {
        $this->type[$call_number] = null;
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

    protected function getTypeDefault()
    {
        return $this->type['_default'];
    }

    protected function resetTypeDefault()
    {
        $this->type['_default'] = null;
    }

    /**
     * Set method type on specific call
     *
     * @param int $call_number
     * @param array $type
     */
    protected function setTypeCall(int $call_number, array $type)
    {
        $this->type[$call_number] = $type;
    }

    protected function getTypeCall(int $call_number)
    {
        return $this->type[$call_number] ?? null;
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

    protected function throwInvalidRangeException()
    {
        throw new InvalidArgumentException('Invalid "range" argument. Should be "i", ">i", "<i" ">=i", "<=i", "i-j" (where i/j are positive integers), callable or instance of ' . Constraint::class);
    }
}
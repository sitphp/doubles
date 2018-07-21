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

class ExpectationCollection
{
    protected $expectations = [];

    /**
     * Make expectation methods dummies for a specific call
     * or for all calls if $call_count is null
     *
     * @param null $call_number
     * @return ExpectationCollection
     */
    function dummy($call_number = null)
    {
        foreach ($this->expectations as $expectation) {
            $expectation->dummy($call_number);
        }
        return $this;
    }

    /**
     * Make expectation methods mocks for a specific call
     * or for all calls if $call_count is null
     *
     * @param null $call_number
     * @return ExpectationCollection
     */
    function mock($call_number = null)
    {
        foreach ($this->expectations as $expectation) {
            $expectation->mock($call_number);
        }
        return $this;
    }

    /**
     * Make expectation methods stubs that runs a specific callback
     * for a specific call or for all calls if $call_count is null
     *
     * @param $return
     * @param null $call_number
     * @return ExpectationCollection
     */
    function stub($return, $call_number = null)
    {
        foreach ($this->expectations as $expectation) {
            $expectation->stub($return, $call_number);
        }
        return $this;
    }

    /**
     * Set and validate expectation methods count
     *
     * @param $range
     * @return $this
     * @throws \Exception
     * @return ExpectationCollection
     */
    function count($range)
    {
        foreach ($this->expectations as $expectation) {
            $expectation->count($range);
        }
        return $this;
    }

    /**
     * Set expectations arguments assertions callbacks
     *
     * @param $arguments_assertions
     * @param null $call_number
     * @return ExpectationCollection
     */
    function args($arguments_assertions, $call_number = null)
    {
        foreach ($this->expectations as $expectation) {
            $expectation->args($arguments_assertions, $call_number);
        }
        return $this;
    }

    /**
     * Add an expectation to the collection
     *
     * @param Expectation $expectation
     */
    public function add(Expectation $expectation)
    {
        $this->expectations[] = $expectation;
    }
}

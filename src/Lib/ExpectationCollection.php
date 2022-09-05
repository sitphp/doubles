<?php
/**
 * This file is part of the "sitphp/doubles" package.
 *
 * @license MIT License
 * @link https://github.com/sitphp/doubles
 * @copyright Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 */

namespace SitPHP\Doubles\Lib;

use \Exception;

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
    function dummy($call_number = null): ExpectationCollection
    {
        /** @var Expectation $expectation */
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
    function mock($call_number = null): ExpectationCollection
    {
        /** @var Expectation $expectation */
        foreach ($this->expectations as $expectation) {
            $expectation->mock($call_number);
        }
        return $this;
    }

    function default($call_number = null): ExpectationCollection
    {
        /** @var Expectation $expectation */
        foreach ($this->expectations as $expectation) {
            $expectation->default($call_number);
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
    function return($return, $call_number = null): ExpectationCollection
    {
        /** @var Expectation $expectation */
        foreach ($this->expectations as $expectation) {
            $expectation->return($return, $call_number);
        }
        return $this;
    }

    /**
     * Set and validate expectation methods count
     *
     * @param $range
     * @return $this
     * @return ExpectationCollection
     * @throws Exception
     */
    function count($range): ExpectationCollection
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
    function args($arguments_assertions, $call_number = null): ExpectationCollection
    {
        /** @var Expectation $expectation */
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

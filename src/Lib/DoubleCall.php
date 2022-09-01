<?php

namespace Doubles\Lib;

class DoubleCall
{
    private $method;
    private $args = [];

    function __construct(string $method, array $args = [])
    {
        $this->method = $method;
        $this->args = $args;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }
}
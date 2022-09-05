<?php
/**
 * This file is part of the "sitphp/doubles" package.
 *
 * @license MIT License
 * @link https://github.com/sitphp/doubles
 * @copyright Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 */

namespace SitPHP\Doubles;

class TestCase extends \PHPUnit\Framework\TestCase
{
    function tearDown(): void
    {
        parent::tearDown();
        Double::close();
    }
}

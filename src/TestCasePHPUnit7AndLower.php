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

namespace Doublit;


class TestCasePHPUnit7AndLower extends \PHPUnit\Framework\TestCase
{
    function tearDown()
    {
        parent::tearDown();
        Doublit::close();
    }
}
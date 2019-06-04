<?php
/**
 * This file is part of the "sitphp/doubles" package.
 *
 *  @license MIT License
 *  @link https://github.com/sitphp/doubles
 *  @copyright Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 */

namespace Doubles;


if (version_compare(\PHPUnit\Runner\Version::id(), '8.0.0', '>=')) {
    class_alias(TestCasePHPUnit8AndHigher::class, DoulitTestCase::class);
} else {
    class_alias(TestCasePHPUnit7AndLower::class, DoulitTestCase::class);
}


class TestCase extends DoulitTestCase
{

}

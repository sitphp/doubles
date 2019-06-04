<?php
/**
 * This file is part of the "sitphp/doubles" package.
 *
 *  @license MIT License
 *  @link https://github.com/sitphp/doubles
 *  @copyright Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 */

namespace Doubles\Lib;


class EvalLoader
{

    /**
     * Load php code
     *
     * @param $code
     */
    public static function load($code)
    {
        eval('?>' . $code);
    }
}
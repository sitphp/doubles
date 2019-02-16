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


class EvalLoader
{

    /**
     * Load php code
     *
     * @param $code
     */
    public static function load($code)
    {
        echo $code;
        eval('?>' . $code);
    }
}
<?php
/**
 * This file is part of the "sitphp/doubles" package.
 *
 * @license MIT License
 * @link https://github.com/sitphp/doubles
 * @copyright Alexandre Geiswiller <alexandre.geiswiller@gmail.com>
 */

$vendor_folder = getenv('COMPOSER_VENDOR_DIR') ?: 'vendor';
$composer_autoload_path = dirname(dirname(__FILE__)) . '/' . $vendor_folder . '/' . 'autoload.php';

if (!file_exists($composer_autoload_path)) {
    throw new RuntimeException('Path to composer autoload file not found at :' . $composer_autoload_path);
}
require_once $composer_autoload_path;

unset($vendor_folder, $composer_autoload_path);

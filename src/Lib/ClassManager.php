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

use Doublit\Exceptions\RuntimeException;

class ClassManager
{
    protected static $reflection_classes;

    /**
     * Get class reflection
     *
     * @param string $class
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    public static function getReflection(string $class)
    {
        if (!isset(self::$reflection_classes[$class])) {
            self::$reflection_classes[$class] = new \ReflectionClass($class);
        }
        return self::$reflection_classes[$class];
    }

    /**
     * Check if a given class has finals calls
     *
     * @param $class
     * @return bool
     * @throws \ReflectionException
     */
    public static function hasFinalCalls($class)
    {
        $reflection_class = self::getReflection($class);
        if ($reflection_class->isFinal()) {
            return true;
        }
        $reflection_methods = $reflection_class->getMethods();
        foreach ($reflection_methods as $method) {
            if ($method->isFinal()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if a given class has abstract calls
     *
     * @param $class
     * @return bool
     * @throws \ReflectionException
     */
    public static function hasAbstractCalls($class)
    {
        $reflection_class = self::getReflection($class);
        if ($reflection_class->isAbstract()) {
            return true;
        }
        $reflection_methods = $reflection_class->getMethods();
        foreach ($reflection_methods as $method) {
            if ($method->isAbstract()) {
                return true;
            }
        }
        return false;
    }

    public static function parseClass($class)
    {
        $class_parts = explode('\\', trim($class, '\\'));
        $short_name = end($class_parts);
        array_pop($class_parts);
        if (!empty($class_parts)) {
            $namespace = trim(implode('\\', $class_parts), '\\');
        } else {
            $namespace = null;
        }
        return ['short_name' => $short_name, 'namespace' => $namespace];
    }

    /**
     * Normalize a class
     *
     * @param $class
     * @return string
     */
    public static function normalizeClass($class)
    {
        $class = trim($class, '\\');
        $class = '\\' . $class;
        return $class;
    }

    /**
     * Get code from specific class
     *
     * @param $class
     * @param null $options
     * @return bool|null|string|string[]
     * @throws \ReflectionException
     */
    public static function getCode($class, $options = null)
    {
        $reflection_class = self::getReflection($class);
        $class_path = $reflection_class->getFileName();
        $class_code = file_get_contents($class_path);

        // Clean other classes
        preg_match_all('#^(?:final\s+)?(?:abstract\s+)?(?:class|interface|trait)(?:(?!\s+' . $reflection_class->getShortName() . '))\s+[a-zA-Z\d_\s]*{$#sm', $class_code, $matches, PREG_OFFSET_CAPTURE);
        if (!empty($matches)) {
            $classes_to_remove = [];
            foreach ($matches[0] as $match) {
                $beginning = false;
                $brace_count = 0;
                $offset_start = $match[1];
                $offset_end = $offset_start;
                $count_braces = true;
                while ($beginning === false || $brace_count != 0) {
                    $offset_end++;
                    if ($beginning === false && $class_code[$offset_end] == '}') {
                        throw new RuntimeException('Class "' . $class . '" code could not be extracted : wrong pattern');
                    }
                    if ($class_code[$offset_end] == '"' || $class_code[$offset_end] == "'") {
                        $count_braces = $count_braces ? false : true;
                    }
                    if ($class_code[$offset_end] == '{' && $count_braces) {
                        $beginning = true;
                        $brace_count++;
                    } else if ($class_code[$offset_end] == '}' && $count_braces) {
                        $brace_count--;
                    }
                }
                $offset_end++;
                $classes_to_remove[] = substr($class_code, $offset_start, $offset_end - $offset_start);
            }
            foreach ($classes_to_remove as $class_to_remove) {
                $class_code = str_replace($class_to_remove, '', $class_code);
            }
        }

        // Clean final calls
        if (isset($options['clean_final']) && $options['clean_final']) {
            if ($reflection_class->isFinal()) {
                $class_code = preg_replace('#final\s+class\s+' . $reflection_class->getShortName() . '#', 'class ' . $reflection_class->getShortName(), $class_code);
            }
            foreach ($reflection_class->getMethods() as $reflection_method) {
                if ($reflection_method->isFinal()) {
                    if ($reflection_method->isStatic()) {
                        $class_code = preg_replace('#final\s+(public|protected|private)\s+static\s+function\s+' . $reflection_method->getName() . '#', '$1 static function ' . $reflection_method->getName(), $class_code);
                    } else {
                        $class_code = preg_replace('#final\s+(?:(public|protected|private)\s+)?function\s+' . $reflection_method->getName() . '#', '$1 function ' . $reflection_method->getName(), $class_code);
                    }
                }
            }
        }

        // Clean abstract classes
        if (isset($options['clean_abstract']) && $options['clean_abstract']) {
            if ($reflection_class->isAbstract()) {
                $class_code = preg_replace('#abstract\s+(class|trait)\s+' . $reflection_class->getShortName() . '#', '$1 ' . $reflection_class->getShortName(), $class_code);
            }
            foreach ($reflection_class->getMethods() as $reflection_method) {
                if ($reflection_method->isAbstract()) {
                    $class_code = preg_replace('#abstract\s+(?:(public|protected|private)\s+)?(?:(static)\s+)?function\s+' . $reflection_method->getName() . '\s*\(\s*\)\s*;#', '$1 $2 function ' . $reflection_method->getName() . '(){}', $class_code);
                }
            }
        }
        return $class_code;
    }
}

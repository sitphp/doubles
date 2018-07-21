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

use Doublit\Lib\DoubleStub;
use \Doublit\Lib\EvalLoader;
use \Doublit\Lib\ClassManager;
use \Doublit\Exceptions\InvalidArgumentException;
use \Doublit\Exceptions\RuntimeException;

class Doublit
{

    protected static $type_hints = ['self', 'array', 'callable', 'boot', 'float', 'int', 'string', 'iterable'];
    protected static $count = 0;
    protected static $config = [
        'allow_final_doubles' => true,
        'allow_non_existent_classes' => true,
        'allow_protected_methods' => false,
        'test_unexpected_methods' => false,
    ];
    protected static $reflection_classes = [];
    protected static $doubles = [];
    protected static $instances = [];
    protected static $services = [
        'class_manager' => ClassManager::class
    ];

    /**
     * Set config
     *
     * @param $label
     * @param null $value
     */
    public static function config($label, $value = null)
    {
        if ($value === null) {
            if (!is_array($label)) {
                throw new InvalidArgumentException('Invalid "label" argument : should be an array when "value" is null');
            }
            foreach ($label as $key => $value) {
                self::config($key, $value);
                return;
            }
        }
        if (!is_string($label)) {
            throw new InvalidArgumentException('Invalid "label" argument : should be a string');
        }
        self::validate($label, $value);
        self::$config[$label] = $value;
    }

    /**
     * Get config
     *
     * @param string|null $label
     * @return array|mixed|null
     */
    protected static function getConfig($label = null)
    {
        if (isset($label) && !is_string($label)) {
            throw new InvalidArgumentException('Invalid "label" argument : should be a string');
        }
        if (!isset($label)) {
            return self::$config;
        }
        $config_parts = explode('.', $label);
        $config = &self::$config;
        foreach ($config_parts as $config_part) {
            if (!isset($config[$config_part])) {
                return null;
            }
            $config = &$config[$config_part];
        }
        return $config;
    }

    /**
     * Validate config value
     *
     * @param $label
     * @param $value
     */
    protected static function validate($label, $value)
    {
        switch ($label) {
            case 'type':
                if (!in_array($value, ['dummy', 'mock', 'alias'])) {
                    throw new InvalidArgumentException('Invalid config value for label "' . $label . '".');
                }
                break;
            case 'class':
                if (!is_string($value)) {
                    throw new InvalidArgumentException('Invalid "class" argument : should be a string.');
                }
                break;
            case 'implements':
                if ($value !== null && !is_string($value) && !is_array($value)) {
                    throw new InvalidArgumentException('Invalid config value for "' . $label . '"": should be null, array or string.');
                }
                break;
            case 'allow_final_doubles':
            case 'test_unexpected_methods':
            case 'allow_protected_methods':
            case 'allow_non_existent_classes':
                if (!is_bool($value)) {
                    throw new InvalidArgumentException('Invalid config value for "' . $label . '" : should be a boolean.');
                }
                break;

            default:
                throw new InvalidArgumentException('Invalid config key "' . $label);
        }
    }

    /**
     * Make a class double and return its definition
     *
     * @param $type
     * @param string $class
     * @param array $implements
     * @param array|null $config self::$config
     * @return array|mixed
     * @throws \ReflectionException
     */
    protected static function makeDouble($type, $class, $implements = null, array $config = null)
    {
        self::validate('class', $class);
        self::validate('type', $type);
        self::validate('implements', $implements);
        if (isset($config)) {
            foreach ($config as $key => $value) {
                self::validate($key, $value);
            }
        }

        // Double definition
        $double_definition = self::resolveBaseDoubleDefinition($type, $class, $implements, $config);
        $double_definition = self::resolveMethodsToImplement($double_definition);

        // Load double
        $code = self::resolveDoubleCode($double_definition);
        EvalLoader::load($code);

        // Prepare double
        /* @var $double DoubleStub */
        $double = $double_definition['class_name'];
        $double::_doublit_initialize($double_definition['config']);

        // Save double definition
        self::addDouble($double_definition['class_name'], $double_definition);

        return $double_definition;
    }

    /**
     * Return a double class name
     *
     * @param $type
     * @param string $class
     * @param array $implements
     * @param array|null $config self::$config
     * @return DoubleStub $double DoubleStub
     * @throws \ReflectionException
     */
    public static function name($type, $class, $implements = null, array $config = null)
    {
        $double_definition = self::makeDouble($type, $class, $implements, $config);
        /* @var $double DoubleStub */
        $double = $double_definition['class_name'];
        return $double;
    }

    /**
     * Shortcut to name('mock', ...) method
     *
     * @param $class
     * @param array|null $implements
     * @param array|null $config
     * @return DoubleStub
     * @throws \ReflectionException
     */
    public static function mock_name($class, $implements = null, array $config = null)
    {
        return self::name('mock', $class, $implements, $config);
    }

    /**
     * Shortcut to name('dummy', ...) method
     *
     * @param $class
     * @param array|null $implements
     * @param array|null $config
     * @return DoubleStub
     * @throws \ReflectionException
     */
    public static function dummy_name($class, $implements = null, array $config = null)
    {
        return self::name('dummy', $class, $implements, $config);
    }

    /**
     * Shortcut to name('alias', ...) method
     *
     * @param $class
     * @param array|null $implements
     * @param array|null $config
     * @return DoubleStub
     * @throws \ReflectionException
     */
    public static function alias_name($class, $implements = null, array $config = null)
    {
        return self::name('alias', $class, $implements, $config);
    }

    /**
     * Return a double instance
     *
     * @param $type
     * @param string $class
     * @param array|null $arguments
     * @param array $implements
     * @param null $config
     * @return DoubleStub $instance
     * @throws \ReflectionException
     */
    public static function instance($type, $class, array $arguments = null, $implements = null, $config = null)
    {
        /* @var $double DoubleStub */

        $double_definition = self::makeDouble($type, $class, $implements, $config);
        $double = $double_definition['class_name'];
        if (isset($arguments)) {
            if (method_exists($double, '__construct')) {
                $double::_method('__construct')->mock();
            }
            $instance = new $double(...$arguments);
        } else {
            if (method_exists($double, '__construct')) {
                $double::_method('__construct')->dummy();
            }
            $instance = new $double();
        }
        self::addInstance($double, $instance);
        return $instance;
    }

    /**
     * Shortcut to instance('mock', ...) method
     *
     * @param $class
     * @param array|null $arguments
     * @param array|null $implements
     * @param array|null $config
     * @return DoubleStub
     * @throws \ReflectionException
     */
    public static function mock_instance($class, array $arguments = null, $implements = null, array $config = null)
    {
        return self::instance('mock', $class, $arguments, $implements, $config);
    }

    /**
     * Shortcut to instance('dummy', ...) method
     *
     * @param $class
     * @param array|null $arguments
     * @param null $implements
     * @param array|null $config
     * @return DoubleStub
     * @throws \ReflectionException
     */
    public static function dummy_instance($class, array $arguments = null, $implements = null, array $config = null)
    {
        return self::instance('dummy', $class, $arguments, $implements, $config);
    }

    /**
     * Shortcut to instance('alias', ...) method
     *
     * @param $class
     * @param array|null $arguments
     * @param null $implements
     * @param array|null $config
     * @return DoubleStub
     * @throws \ReflectionException
     */
    public static function alias_instance($class, array $arguments = null, $implements = null, array $config = null)
    {
        return self::instance('alias', $class, $arguments, $implements, $config);
    }

    /**
     * Resolve double code from definition
     *
     * @param $double_definition
     * @return bool|mixed|string
     */
    protected static function resolveDoubleCode($double_definition)
    {
        /* @var $method \ReflectionMethod */

        if (class_exists($double_definition['class_name'], false)) {
            throw new InvalidArgumentException('Cannot make double with name "' . $double_definition['short_name'] . '" : class name already taken');
        }
        $code = file_get_contents(__DIR__ . '/Lib/DoubleStub.stub');
        if (isset($double_definition['namespace'])) {
            $code = str_replace('namespace Doublit\Lib;', 'namespace ' . $double_definition['namespace'] . ';', $code);
        } else {
            $code = str_replace('namespace Doublit\Lib;', '', $code);
        }
        $class_code = 'class ' . trim($double_definition['short_name'], '\\');
        if (isset($double_definition['extends'])) {
            $class_code .= ' extends ' . $double_definition['extends'];
        }
        if (isset($double_definition['interfaces'])) {
            $class_code .= ' implements ' . implode(',', $double_definition['interfaces']);
        }
        $class_code .= '{';
        if (isset($double_definition['traits'])) {
            $class_code .= PHP_EOL . 'use ' . implode(',', $double_definition['traits']) . ';';
        }
        $code = preg_replace('#class\s+DoubleStub\s*{#', $class_code, $code);

        if (isset($double_definition['methods'])) {
            $implemented_methods = [];
            $methods_code = [];
            foreach ($double_definition['methods'] as $method) {
                $reference_params = [];

                // Check method was not already implemented
                $method_name = $method instanceof \ReflectionMethod ? $method->getShortName() : $method;
                if (in_array($method_name, $implemented_methods)) {
                    throw new InvalidArgumentException('Trying to implement method "' . $method_name . '" twice');
                }
                $implemented_methods[] = $method_name;

                // Build method
                if ($method instanceof \ReflectionMethod) {
                    if ($method->isFinal()) {
                        continue;
                    }
                    if ($method->isProtected()) {
                        $method_code = 'protected';
                    } else if ($method->isPrivate()) {
                        $method_code = 'private';
                    } else {
                        $method_code = 'public';
                    }
                    if ($method->isStatic()) {
                        $method_code .= ' static';
                    }
                    $method_code .= ' function ' . $method->getShortName() . '(';
                    $params = ($method->getShortName() != '__construct') ? $method->getParameters() : [];
                    $first_param = true;
                    foreach ($params as $key => $param) {
                        if (!$first_param) {
                            $method_code .= ', ';
                        }
                        if ($first_param) {
                            $first_param = false;
                        }
                        if ($param->hasType()) {
                            $param_type = $param->getType();
                            if (!in_array($param_type, self::$type_hints)) {
                                $param_type = ClassManager::normalizeClass($param->getClass()->getName());
                            }
                            $method_code .= $param_type . ' ';
                        }
                        if ($param->isPassedByReference()) {
                            $reference_params[$key] = $param->getName();
                            $method_code .= '&';
                        }
                        if ($param->isVariadic()) {
                            $method_code .= '...$' . $param->getName();
                        } else if ($param->isOptional()) {
                            $method_code .= '$' . $param->getName() . ' = ';
                            if ($param->isDefaultValueAvailable()) {
                                $method_default_value = $param->getDefaultValue();
                                $method_code .= $method_default_value === null ? 'null' : '"' . $method_default_value . '"';
                            } else {
                                $method_code .= 'null';
                            }
                        } else {
                            $method_code .= '$' . $param->getName();
                        }
                    }
                    $method_code .= ')';
                    if ($method->hasReturnType()) {
                        $method_return_type = $method->getReturnType();
                        $method_code .= ' : ' . $method_return_type;
                    }
                    $is_static = $method->isStatic();
                } else if (is_string($method)) {
                    $method_code = 'public';
                    if (substr($method, 0, 7) == 'static:') {
                        $method = substr($method, 7);
                        $method_code .= ' static function ' . $method . '()';
                        $is_static = true;
                    } else {
                        $method_code .= ' function ' . $method . '()';
                        $is_static = false;
                    }
                } else {
                    throw new RuntimeException('Wrong method format');
                }
                $method_code .= '{ $args = func_get_args(); ';
                foreach ($reference_params as $key => $reference_param) {
                    $method_code .= '$args[' . $key . '] = &$' . $reference_param . '; ';
                }
                if ($is_static) {
                    $method_code .= '$return = self::_doublit_handleStaticCall(__FUNCTION__, $args); ';
                } else {
                    $method_code .= '$return = $this->_doublit_handleInstanceCall(__FUNCTION__, $args); ';
                }
                $method_code .= 'return $return; }';
                $methods_code[] = $method_code;
            }
            $code = substr($code, 0, strrpos($code, "}")) . implode($methods_code, PHP_EOL) . '}';
        }
        return $code;
    }

    /**
     * Resolve double class base definition
     *
     * @param $type
     * @param $class
     * @param $implements
     * @param null $config
     * @return array
     * @throws \ReflectionException
     */
    protected static function resolveBaseDoubleDefinition($type, $class, $implements = null, $config = null)
    {

        $allow_final_doubles = isset($config['allow_final_doubles']) ? $config['allow_final_doubles'] : self::getConfig('allow_final_doubles');
        $allow_non_existent_classes = isset($config['allow_non_existent_classes']) ? $config['allow_non_existent_classes'] : self::getConfig('allow_non_existent_classes');

        // Check for methods to implement
        if (preg_match('#\[([a-zA-Z0-9_\x7f-\xff,\s:]*)](?=\s*$)#', $class, $match)) {
            $class = str_replace($match[0], '', $class);
            $double_methods = array_map('trim', explode(',', trim($match[0], '[]')));
        } else {
            $double_methods = null;
        }

        // Normalize and initialize things
        $double_short_name = null;
        $double_namespace = null;
        $double_extends = null;
        $class = explode(':', trim($class), 2);
        if (isset($class[1])) {
            $original = ClassManager::normalizeClass($class[1]);
            $class_parse = ClassManager::parseClass($class[0]);
            $double_short_name = $class_parse['short_name'];
            $double_namespace = $class_parse['namespace'];
        } else {
            $original = ClassManager::normalizeClass($class[0]);
        }


        if (is_string($implements)) {
            $implements = [$implements];
        }
        $double_traits = [];
        $double_interfaces = [];
        if (isset($implements)) {
            foreach ($implements as $implement) {
                if (trait_exists($implement)) {
                    $double_traits[] = ClassManager::normalizeClass($implement);
                } else {
                    $double_interfaces[] = ClassManager::normalizeClass($implement);
                }
            }
        }
        $double_interfaces[] = '\Doublit\Lib\DoubleInterface';

        if ($type == 'alias') {
            if (class_exists($original, false)) {
                throw new InvalidArgumentException('Unable to make class alias of ' . $original . ' : class was already loaded');
            }
            if (isset($double_short_name)) {
                throw new InvalidArgumentException('Cannot make named doubles of type "alias"');
            }
            if (!$allow_non_existent_classes) {
                throw new InvalidArgumentException('Class ' . $original . ' doesn\'t exist. Set the "allow_non_existent_classes" config parameter to allow creating non existent class doubles');
            }

            $class_parse = ClassManager::parseClass($original);
            $double_short_name = $class_parse['short_name'];
            $double_namespace = $class_parse['namespace'];
        } else if (class_exists($original)) {
            $reflection_class = ClassManager::getReflection($original);
            if ($reflection_class->isFinal() && !$allow_final_doubles) {
                throw new InvalidArgumentException('Class "' . $original . '" is marked final and cannot be doubled. Set config parameter "allow_asserting_final_methods" to "true" to allow doubles of final classes');
            }
            if ($allow_final_doubles && !$reflection_class->isInternal() && ClassManager::hasFinalCalls($original)) {
                $new_class_name = self::generateDoublitClassName();
                $new_class_code = ClassManager::getCode($original, ['clean_final' => true]);
                $new_class_code = preg_replace('#namespace\s+' . str_replace('\\', '\\\\', $reflection_class->getNamespaceName()) . '\s*;#', '', $new_class_code);
                $new_class_code = preg_replace('#class\s+' . $reflection_class->getShortName() . '\s*{#', 'class ' . $new_class_name . '{', $new_class_code);
                EvalLoader::load($new_class_code);
                $double_extends = $new_class_name;
            } else {
                $double_extends = $original;
            }
        } else if (trait_exists($original)) {
            $reflection_class = ClassManager::getReflection($original);
            if ($allow_final_doubles && !$reflection_class->isInternal() && ClassManager::hasFinalCalls($original)) {
                $new_class_name = self::generateDoublitClassName($reflection_class->getName());
                $new_class_code = ClassManager::getCode($original, ['clean_final' => true]);
                $new_class_code = preg_replace('#namespace\s+' . str_replace('\\', '\\\\', $reflection_class->getNamespaceName()) . '\s*;#', '', $new_class_code);
                $replacement = '';
                if (ClassManager::hasAbstractCalls($original)) {
                    $replacement .= 'abstract ';
                }
                $replacement .= 'class '.$new_class_name . '{';
                $new_class_code = preg_replace('#trait\s+' . $reflection_class->getShortName() . '\s*{#', $replacement, $new_class_code);
            } else {
                $reflection_class = ClassManager::getReflection($original);
                $new_class_name = self::generateDoublitClassName($reflection_class->getName());
                $new_class_code = '<?php ';
                if (ClassManager::hasAbstractCalls($original)) {
                    $new_class_code .= 'abstract ';
                }
                $new_class_code .= 'class ' . $new_class_name . ' { use ' . $original . '; }';
            }
            EvalLoader::load($new_class_code);
            $double_extends = $new_class_name;
        } else if (interface_exists($original)) {
            $double_interfaces[] = $original;
        } else {
            throw new InvalidArgumentException('Class/trait/interface ' . $original . ' doesn\'t exist. Use the "instance_alias" or "double_alias" method to create non existent class/trait/interface doubles');
        }

        $double_config = [
            'type' => $type,
            'reference' => isset($reference) ? $reference : null,
            'allow_protected_methods' => isset($config['allow_protected_methods']) ? $config['allow_protected_methods'] : self::getConfig('allow_protected_methods'),
            'test_unexpected_methods' => isset($config['test_unexpected_methods']) ? $config['test_unexpected_methods'] : self::getConfig('test_unexpected_methods')
        ];
        $double_short_name = isset($double_short_name) ? $double_short_name : self::generateDoublitClassName($original);
        $double_definition = [
            'original' => $original,
            'short_name' => $double_short_name,
            'namespace' => $double_namespace,
            'class_name' => isset($double_namespace) ? ClassManager::normalizeClass($double_namespace . '\\' . $double_short_name) : ClassManager::normalizeClass($double_short_name),
            'extends' => $double_extends,
            'interfaces' => !empty($double_interfaces) ? $double_interfaces : null,
            'traits' => !empty($double_traits) ? $double_traits : null,
            'use' => !empty($double_use) ? $double_use : null,
            'methods' => $double_methods,
            'config' => $double_config
        ];
        return $double_definition;
    }

    /**
     * Resolve double class methods to implement from base double definition
     *
     * @param $double_definition
     * @return mixed
     * @throws \ReflectionException
     */
    protected static function resolveMethodsToImplement($double_definition)
    {
        $methods = isset($double_definition['methods']) ? $double_definition['methods'] : [];
        // Resolve interface methods
        if (isset($double_definition['interfaces'])) {
            foreach ($double_definition['interfaces'] as $interface) {
                if (!interface_exists($interface)) {
                    continue;
                }
                $reflection_interface = ClassManager::getReflection($interface);
                $interface_methods = $reflection_interface->getMethods();
                foreach ($interface_methods as $interface_method) {
                    if (isset($double_definition['extends']) && method_exists($double_definition['extends'], $interface_method->name)) {
                        continue;
                    }
                    $reflection_method = new \ReflectionMethod($interface, $interface_method->name);
                    $methods[] = $reflection_method;
                }
            }
        }

        // Resolve extend methods
        if (isset($double_definition['extends']) && class_exists($double_definition['extends'])) {
            $reflection_extends = ClassManager::getReflection($double_definition['extends']);
            $extend_methods = $reflection_extends->getMethods();
            foreach ($extend_methods as $extend_method) {
                $reflection_method = new \ReflectionMethod($double_definition['extends'], $extend_method->name);
                $methods[] = $reflection_method;
            }
        }
        if (!empty($methods)) {
            $double_definition['methods'] = $methods;
        }
        return $double_definition;
    }

    /**
     * Generate a new double class name
     *
     * @param null $from
     * @return string
     */
    protected static function generateDoublitClassName($from = null)
    {
        $class_name = 'Doublit_' . self::$count;
        if ($from) {
            $class_name .= '_' . str_replace('\\', '_', trim($from, '\\'));
        }
        self::$count++;
        return $class_name;
    }

    /**
     * Save a double class definition
     *
     * @param $label
     * @param $double
     */
    protected static function addDouble($label, $double)
    {
        self::$doubles[$label] = $double;
    }

    /**
     * Get double class definition
     *
     * @param null $label
     * @return array|mixed
     */
    protected static function getDouble($label = null)
    {
        return isset($label) ? self::$doubles[$label] : self::$doubles;
    }

    /**
     * Save a double instance
     *
     * @param $label
     * @param $instance
     */
    protected static function addInstance($label, $instance)
    {
        self::$instances[$label] = $instance;
    }

    /**
     * Get a double instance
     *
     * @param null $label
     * @return array|mixed
     */
    public static function getInstance($label = null)
    {
        if (isset($label)) {
            return isset(self::$instances[$label]) ? self::$instances[$label] : null;
        }
        return self::$instances;
    }

    public static function close()
    {
        /* @var $double DoubleStub */
        foreach (self::getDouble() as $double_definition) {
            $double = $double_definition['class_name'];
            $double::_doublit_close();
        }
    }
}

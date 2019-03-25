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

use \Doublit\Exceptions\LogicException;
use Doublit\Lib\DoubleInterface;
use \Doublit\Lib\DoubleStub;
use \Doublit\Lib\EvalLoader;
use \Doublit\Lib\ClassManager;
use \Doublit\Exceptions\InvalidArgumentException;
use \Doublit\Exceptions\RuntimeException;

class Doublit
{

    protected static $type_hints = ['self', 'array', 'callable', 'bool', 'float', 'int', 'string'];
    protected static $count = 0;
    protected static $config_mapping = [
        'allow_final_doubles' => 'allowFinalDoubles',
        'allow_non_existent_classes' => 'allowNonExistentClasses',
        'allow_protected_methods' => 'allowProtectedMethods',
        'test_unexpected_methods' => 'testUnexpectedMethods',
    ];
    protected static $reflection_classes = [];
    protected static $doubles = [];
    protected static $instances = [];
    protected static $services = [
        'class_manager' => ClassManager::class
    ];

    protected $original;
    protected $interfaces = [];
    protected $traits = [];
    protected $methods = [];
    protected $class_name;
    protected $allow_final_doubles = true;
    protected $test_unexpected_methods = false;
    protected $allow_protected_methods = false;
    protected $allow_non_existent_classes = true;
    protected $type;
    protected $class_type = 'class';

    /**
     * Set config mapping
     *
     * @param string $config_label
     * @param string $method
     */
    static function setConfigMapping(string $config_label, string $method)
    {
        if (!method_exists(static::class, $method)) {
            throw new InvalidArgumentException('Invalid "' . $config_label . '" config mapping : method "' . $method . '" doesnt exist');
        }
        self::$config_mapping[$config_label] = $method;
    }

    /**
     * Build dummy type double
     *
     * @param string $class
     * @param array|null $config
     * @return Doublit
     */
    static function dummy(string $class, array $config = null)
    {
        return new self('dummy', $class, $config);
    }

    /**
     * Build mock type double
     *
     * @param string $class
     * @param array|null $config
     * @return Doublit
     */
    static function mock(string $class, array $config = null)
    {
        return new self('mock', $class, $config);
    }

    /**
     * Build alias type double
     *
     * @param string $class
     * @param array|null $config
     * @return Doublit
     */
    static function alias(string $class, array $config = null)
    {
        return new self('alias', $class, $config);
    }

    /**
     * Doublit constructor
     *
     * @param string $type
     * @param string $class
     * @param array|null $config
     */
    function __construct(string $type, string $class, array $config = null)
    {
        $this->setType($type);
        $this->setOriginal($class);
        $this->addInterface(Lib\DoubleInterface::class);

        if (isset($config)) {
            $this->setConfig($config);
        }
    }

    /**
     * Set config values
     *
     * @param array $config
     * @return $this
     */
    function setConfig(array $config)
    {
        foreach ($config as $key => $value) {
            $config_method = self::$config_mapping[$key] ??  null;
            if ($config_method === null) {
                throw new InvalidArgumentException('Invalid config "' . $key . '"');
            }
            $this->$config_method($value);
        }
        return $this;
    }

    /**
     * Set original class
     *
     * @param $original
     */
    protected function setOriginal(string $original){
        if($this->getType() !== 'alias' && is_subclass_of($original,DoubleInterface::class)){
            throw new InvalidArgumentException('Invalid class : cannot make doubles of doubles');
        }
        $this->original = ClassManager::normalizeClass($original);
    }

    /**
     * Get original class
     *
     * @return mixed
     */
    protected function getOriginal(){
        return $this->original;
    }

    /**
     * Set double type
     *
     * @param $type
     */
    protected function setType($type)
    {
        if (!in_array($type, ['alias', 'mock', 'dummy'])) {
            throw new \RuntimeException('Invalid argument type : expected "mock", "dummy" or "alias", found "' . $this->type . '"');
        }
        $this->type = $type;
    }


    /**
     * Get double type
     *
     * @return mixed
     */
    protected function getType()
    {
        return $this->type;
    }

    /**
     * Add interfaces to implement
     *
     * @param $interface
     * @return $this
     */
    function addInterface($interface)
    {
        if (is_string($interface)) {
            $this->interfaces[] = $this->normalizeInterface($interface);
        } else if (is_array($interface)) {
            foreach ($interface as $item) {
                if(!is_string($item)){
                    $this->throwInterfaceException();
                }
                $this->addInterface($item);
            }
        } else {
            $this->throwInterfaceException();
        }
        return $this;
    }
    /**
     * Throw an invalid interface exception
     */
    protected function throwInterfaceException(){
        throw new InvalidArgumentException('Invalid $interface argument type : expected string or array of strings');
    }

    /**
     * Return implemented interface
     *
     * @return array
     */
    function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * Normalize interface
     *
     * @param string $interface
     * @return string
     */
    protected function normalizeInterface(string $interface)
    {
        if (!interface_exists($interface)) {
            throw new InvalidArgumentException('Invalid interface "' . $interface . '"');
        }
        return ClassManager::normalizeClass($interface);
    }

    /**
     * Add trait to implement
     *
     * @param $trait
     * @return $this
     */
    function addTrait($trait)
    {
        if (is_string($trait)) {
            $this->traits[] = $this->normalizeTrait($trait);
        } else if (is_array($trait)) {
            foreach ($trait as $item) {
                if(!is_string($item)){
                    $this->throwTraitException();
                }
                $this->addTrait($item);
            }
        } else {
            $this->throwTraitException();
        }
        return $this;
    }

    /**
     * Throw an invalid trait exception
     */
    protected function throwTraitException(){
        throw new InvalidArgumentException('Invalid $trait argument type : expected string or array of strings');
    }

    /**
     * Get implemented trait
     *
     * @return array
     */
    function getTraits()
    {
        return $this->traits;
    }

    /**
     * Normalize trait
     *
     * @param $trait
     * @return string
     */
    protected function normalizeTrait($trait)
    {
        if (!trait_exists($trait)) {
            throw new InvalidArgumentException('Invalid trait "' . $trait . '"');
        }
        return ClassManager::normalizeClass($trait);
    }

    /**
     * Add method(s) to implement
     *
     * @param $method
     * @return $this
     */
    function addMethod($method)
    {
        if (is_string($method)) {
            $this->methods[] = $this->normalizeMethod($method);
        } else if (is_array($method)) {
            foreach ($method as $item) {
                $this->methods[] = $this->normalizeMethod($item);
            }
        } else {
            throw new \InvalidArgumentException('Invalid $method argument type : expected string or array');
        }
        return $this;
    }

    /**
     * Get implemented methods
     *
     * @return array
     */
    function getMethods()
    {
        return $this->methods;
    }


    /**
     * Normalize method
     *
     * @param string $method
     * @return string
     */
    protected function normalizeMethod(string $method)
    {
        return trim($method);
    }

    /**
     * Make an alias trait
     *
     * @return $this
     */
    function aliasTrait(){
        if ($this->getType() != 'alias') {
            throw new LogicException('Alias class type can only be set for alias doubles');
        }
        $this->class_type = 'trait';
        return $this;
    }

    /**
     * Make an alias class abstract
     *
     * @return $this
     */
    function aliasAbstract(){
        if ($this->getType() != 'alias') {
            throw new LogicException('Alias class type can only be set for alias doubles');
        }
        $this->class_type = 'abstract class';
        return $this;
    }

    function getClassType()
    {
        return $this->class_type;
    }

    /**
     * Set double name
     *
     * @param string $class_name
     * @return $this
     */
    function setName(string $class_name)
    {
        $this->class_name = $class_name;
        return $this;
    }

    /**
     * Get defined double
     *
     * @return mixed
     */
    function getName(){
        return $this->class_name;
    }

    /**
     * Set allow_final_doubles config param
     *
     * @param bool $bool
     * @return $this
     */
    function allowFinalDoubles(bool $bool)
    {
        $this->allow_final_doubles = $bool;
        return $this;
    }

    /**
     * Set test_unexpected_methods config param
     *
     * @param bool $bool
     * @return $this
     */
    function testUnexpectedMethods(bool $bool)
    {
        $this->test_unexpected_methods = $bool;
        return $this;
    }

    /**
     * Set allow_protected_methods config param
     *
     * @param bool $bool
     * @return $this
     */
    function allowProtectedMethods(bool $bool)
    {
        $this->allow_protected_methods = $bool;
        return $this;
    }

    /**
     * Set allow_non allow_non_existent_classes config param
     *
     * @param bool $bool
     * @return $this
     */
    function allowNonExistentClasses(bool $bool)
    {
        $this->allow_non_existent_classes = $bool;
        return $this;
    }

    /**
     * Return double class
     *
     * @return DoubleStub
     * @throws \ReflectionException
     */
    function getClass()
    {
        $double_definition = $this->resolveDoubleDefinition();
        return $this->makeDouble($double_definition);
    }

    /**
     * Return instance of double
     *
     * @param array|null $construct_params
     * @return DoubleStub
     * @throws \ReflectionException
     */
    function getInstance(... $construct_params)
    {
        $double_definition = $this->resolveDoubleDefinition();
        $double = $this->makeDouble($double_definition);
        if (!empty($construct_params)) {
            return new $double(...$construct_params);
        } else {
            $params = $double::_doublit_getMethodTypeDefinition('__construct', 1);
            if ($params[0] == 'dummy') {
                $_doublit_reference_reflexion = new \ReflectionClass($double);
                return $_doublit_reference_reflexion->newInstanceWithoutConstructor();
            }
            return new $double();
        }
    }

    /**
     * Make a class double and return its definition
     *
     * @param array $double_definition
     * @return DoubleStub
     */
    protected function makeDouble(array $double_definition)
    {
        // Load double
        $code = self::resolveDoubleCode($double_definition);
        EvalLoader::load($code);

        $double = isset($double_definition['namespace']) ? $double_definition['namespace'] . '\\' . $double_definition['short_name'] : $double_definition['short_name'];

        // Prepare double
        /* @var $double DoubleStub */
        $double::_doublit_initialize($double_definition['type'], [
            'allow_protected_methods' => $this->allow_protected_methods,
            'test_unexpected_methods' => $this->test_unexpected_methods,
            'reference' => $double_definition['reference']
        ]);

        // Save double with its definition

        self::addDouble($double, $double_definition);

        return $double;
    }

    /**
     * Return double definition array
     *
     * @return array
     * @throws \ReflectionException
     */
    protected function resolveDoubleDefinition()
    {
        $double_definition = [
            'original' => $this->getOriginal(),
            'class_type' => $this->getClassType(),
            'reference' => null,
            'type' => $this->getType(),
            'short_name' => null,
            'namespace' => null,
            'extends' => null,
            'interfaces' => $this->getInterfaces(),
            'traits' => $this->getTraits(),
            'methods' => $this->getMethods()
        ];


        if ($double_definition['type'] === 'alias') {
            $this->populateAliasDoubleDefinition($double_definition);
        } else if (class_exists($double_definition['original'])) {
            $this->populateClassDoubleDefinition($double_definition);
        } else if (trait_exists($double_definition['original'])) {
            $this->populateTraitDoubleDefinition($double_definition);
        } else if (interface_exists($double_definition['original'])) {
            $this->populateInterfaceDoubleDefinition($double_definition);
        } else {
            throw new InvalidArgumentException('Undefined class "'.$double_definition['original'].'" : Use the "alias" method to  create doubles of non existent classes');
        }
        $this->populateMethodsToImplement($double_definition);

        return $double_definition;
    }

    /**
     * Populate alias type double definition with required parameters
     *
     * @param $double_definition
     * @return mixed
     */
    protected function populateAliasDoubleDefinition(&$double_definition)
    {
        $this->class_name = $double_definition['original'];
        $original = $double_definition['original'];

        if (!$this->allow_non_existent_classes) {
            throw new InvalidArgumentException('Class ' . $original . ' doesnt exist. Set config parameter "allow_non_existent_classes" to "true" to allow creating alias class doubles');
        }
        if (isset($this->name)) {
            throw new InvalidArgumentException('Cannot make named "alias" doubles');
        }

        $class_type = $this->getClassType();
        if (($class_type == 'class' || $class_type == 'abstract class') && class_exists($original, false)) {
            throw new InvalidArgumentException('Unable to make class alias of ' . $original . ' : class was already loaded');
        } else if ($class_type == 'trait' && trait_exists($original, false)) {
            throw new InvalidArgumentException('Unable to make trait alias of ' . $original . ' : class was already loaded');
        } else if ($class_type == 'interface' && interface_exists($original, true)) {
            throw new InvalidArgumentException('Unable to make interface alias of ' . $original . ' : class was already loaded');
        }

        $class_definition = $this->resolveClassDefinition($original);

        $double_definition['class_type'] = $class_type;
        $double_definition['short_name'] = $class_definition['short_name'];
        $double_definition['namespace'] = $class_definition['namespace'];

        // Remove interfaces if trait alias
        if($class_type == 'trait'){
            $double_definition['interfaces'] = [];
        }

        return $double_definition;
    }

    /**
     * Populate class double definition with required parameters
     *
     * @param $double_definition
     * @throws \ReflectionException
     */
    protected function populateClassDoubleDefinition(&$double_definition)
    {

        $original = $double_definition['original'];

        $reflection_class = ClassManager::getReflection($original);

        if ($reflection_class->isFinal()) {
            if (!$this->allow_final_doubles) {
                throw new InvalidArgumentException('Cannot make double of class "' . $original . '" because it is marked final. Set config parameter "allow_final_doubles" to "true" to allow doubles of final classes');
            }
            if ($reflection_class->isInternal()) {
                throw new InvalidArgumentException('Cannot make double of class "' . $original . '" because it is internal and marked final.');
            }
        }
        if (ClassManager::hasFinalCalls($original) && $this->allow_final_doubles && !$reflection_class->isInternal()) {
            $new_class_name = self::generateDoublitClassName();
            $new_class_code = ClassManager::getCode($original, ['clean_final' => true]);
            $new_class_code = preg_replace('#class\s+' . $reflection_class->getShortName() . '\s*{#', 'class ' . $new_class_name . '{', $new_class_code);
            EvalLoader::load($new_class_code);
            $double_extends = '';
            if ($reflection_class->inNamespace()) {
                $double_extends .= '\\' . $reflection_class->getNamespaceName() . '\\';
            }
            $double_extends .= $new_class_name;
        } else {
            /* if($reflection_class->isInternal()){
                 $double_definition['reference'] = $original;
             }*/
            $double_extends = $original;
        }

        $class_definition = $this->resolveClassDefinition($original);

        $double_definition['class_type'] = 'class';
        $double_definition['short_name'] = $class_definition['short_name'];
        $double_definition['namespace'] = $class_definition['namespace'];
        $double_definition['extends'] = $double_extends;

    }

    /**
     * Populate trait double definition with required parameters
     *
     * @param $double_definition
     * @throws \ReflectionException
     */
    protected function populateTraitDoubleDefinition(&$double_definition)
    {
        $original = $double_definition['original'];

        $double_extends = '';
        $reflection_class = ClassManager::getReflection($original);
        $new_class_name = self::generateDoublitClassName($reflection_class->getName());
        // Trait has final calls, prepare a similar class without final calls
        if ($this->allow_final_doubles && !$reflection_class->isInternal() && ClassManager::hasFinalCalls($original)) {
            $new_class_code = ClassManager::getCode($original, ['clean_final' => true]);
            $replacement = '';
            if (ClassManager::hasAbstractCalls($original)) {
                $replacement .= 'abstract ';
            }
            $replacement .= 'class ' . $new_class_name . '{';
            $new_class_code = preg_replace('#trait\s+' . $reflection_class->getShortName() . '\s*{#', $replacement, $new_class_code);
            if ($reflection_class->inNamespace()) {
                $double_extends .= '\\' . $reflection_class->getNamespaceName() . '\\';
            }
            $double_extends .= $new_class_name;
        } // Implement trait in a new class
        else {
            $new_class_code = '<?php ';
            if (ClassManager::hasAbstractCalls($original)) {
                $new_class_code .= 'abstract ';
            }
            $new_class_code .= 'class ' . $new_class_name . ' { use ' . $original . '; }';
            $double_extends .= $new_class_name;
        }
        EvalLoader::load($new_class_code);

        $class_definition = $this->resolveClassDefinition($original);

        $double_definition['original'] = $original;
        $double_definition['class_type'] = 'class';
        $double_definition['namespace'] = $class_definition['namespace'];
        $double_definition['short_name'] = $class_definition['short_name'];
        $double_definition['extends'] = $double_extends;
    }


    /**
     * Populate interface double definition with required parameters
     *
     * @param $double_definition
     */
    protected function populateInterfaceDoubleDefinition(&$double_definition)
    {
        $original = $double_definition['original'];

        $class_definition = $this->resolveClassDefinition($original);

        $double_definition['class_type'] = 'class';
        $double_definition['namespace'] = $class_definition['namespace'];
        $double_definition['short_name'] = $class_definition['short_name'];
        $double_definition['interfaces'][] = $original;
    }

    /**
     * Resolve class namespace and short-name
     *
     * @param $class
     * @return array
     */
    protected function resolveClassDefinition(string $class)
    {
        if (null !== $name = $this->getName()) {
            $class_parse = ClassManager::parseClass($name);
            $double_short_name = $class_parse['short_name'];
            $double_namespace = $class_parse['namespace'];
        } else {
            $double_short_name = self::generateDoublitClassName($class);
            $double_namespace = null;
        }
        return ['namespace' => $double_namespace, 'short_name' => $double_short_name];
    }


    /**
     * Resolve double class methods to implement from base double definition
     *
     * @param $double_definition
     * @return mixed
     * @throws \ReflectionException
     */
    protected function populateMethodsToImplement(&$double_definition)
    {
        // Resolve extend methods
        if ($double_definition['extends'] !== null && class_exists($double_definition['extends'])) {
            $reflection_extends = ClassManager::getReflection($double_definition['extends']);
            $extend_methods = $reflection_extends->getMethods();
            foreach ($extend_methods as $extend_method) {
                $reflection_method = new \ReflectionMethod($double_definition['extends'], $extend_method->name);
                $double_definition['methods'][] = $reflection_method;
            }
        }

        foreach ($double_definition['interfaces'] as $interface) {
            $reflection_interface = ClassManager::getReflection($interface);
            $interface_methods = $reflection_interface->getMethods();
            foreach ($interface_methods as $interface_method) {
                // Skip if double will already implement interface method by heritage
                if ($double_definition['extends'] !== null && method_exists($double_definition['extends'], $interface_method->name)) {
                    $extend_method = new \ReflectionMethod($double_definition['extends'], $interface_method->name);
                    if(
                        !$extend_method->isPublic()
                        || ($interface_method->isStatic() && !$extend_method->isStatic())
                        || (!$interface_method->isStatic() && $extend_method->isStatic())
                        || ($interface_method->getReturnType() !== $extend_method->getReturnType())
                    ){
                        throw new InvalidArgumentException('Interface method '.$interface.'::'.$interface_method->name.' is not compatible with class method '.$double_definition['extends'].'::'.$interface_method->name);
                    }
                    continue;
                }
                $reflection_method = new \ReflectionMethod($interface, $interface_method->name);
                $double_definition['methods'][] = $reflection_method;
            }
        }
    }

    protected static function resolveDoubleCode($double_definition)
    {
        /* @var $method \ReflectionMethod */

        $class_name = isset($double_definition['namespace']) ? $double_definition['namespace'] . '\\' . $double_definition['short_name'] : $double_definition['short_name'];
        if (class_exists($class_name, false)) {
            throw new InvalidArgumentException('Cannot make double with name "' . $class_name . '" : class name already taken');
        }
        $code = file_get_contents(__DIR__ . '/Lib/DoubleStub.stub');
        if (isset($double_definition['namespace'])) {
            $code = str_replace('namespace Doublit\Lib;', 'namespace ' . $double_definition['namespace'] . ';', $code);
        } else {
            $code = str_replace('namespace Doublit\Lib;', '', $code);
        }
        $class_code = $double_definition['class_type'] . ' ' . trim($double_definition['short_name'], '\\');
        if (isset($double_definition['extends'])) {
            $class_code .= ' extends ' . $double_definition['extends'];
        }
        if (!empty($double_definition['interfaces'])) {
            $class_code .= ' implements ' . implode(',', $double_definition['interfaces']);
        }
        $class_code .= '{';
        if (!empty($double_definition['traits'])) {
            $class_code .= PHP_EOL . 'use ' . implode(',', $double_definition['traits']) . ';';
        }
        $code = preg_replace('#class\s+DoubleStub\s*{#', $class_code, $code);

        if (!empty($double_definition['methods'])) {
            $implemented_methods = [];
            $methods_code = [];
            foreach ($double_definition['methods'] as $method) {
                $reference_params = [];

                // Check method was not already implemented
                $method_name = $method instanceof \ReflectionMethod ? $method->getShortName() : $method;
                if (in_array($method_name, $implemented_methods)) {
                    throw new InvalidArgumentException('Cannot to implement method "' . $method_name . '" more than one time');
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
                    $method_code .= ' function ';
                    if($method->returnsReference()){
                        $method_code.='&';
                    }
                    $method_code .= $method->getShortName() . '(';
                    $first_param = true;
                    foreach ($method->getParameters() as $key => $param) {
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
                            } else if (isset($double_definition['extends']) && $param_type == 'self') {
                                $param_type = $double_definition['extends'];
                            }
                            $method_code .= $param_type . ' ';
                        }
                        if ($param->isPassedByReference()) {
                            $reference_params[$key] = $param->getName();
                            $method_code .= '&';
                        }
                        if ($param->isVariadic()) {
                            $method_code .= '...';
                        }
                        $method_code .= '$' . $param->getName();
                        if ($param->isDefaultValueAvailable()) {
                            $method_code .= ' = ';
                            $method_default_value = $param->getDefaultValue();
                            if ($method_default_value === null) {
                                $method_code .= 'null';
                            } else if (is_string($method_default_value)) {
                                $method_code .= '"' . addslashes($method_default_value) . '"';
                            } else if (is_bool($method_default_value)) {
                                $method_code .= $method_default_value ? 'true' : 'false';
                            } else if (is_array($method_default_value)) {
                                $method_code .= self::arrayToString($method_default_value);
                            } else if (is_numeric($method_default_value)) {
                                $method_code .= $method_default_value;
                            } else {
                                $method_code .= var_export($method_default_value);
                            }
                        } else if ($param->isOptional() && !$param->isVariadic()) {
                            $method_code .= ' = null';
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
                    throw new RuntimeException('Invalid method format');
                }
                $method_code .= '{ $args = func_get_args(); ';
                foreach ($reference_params as $key => $reference_param) {
                    $method_code .= 'if(isset($args['.$key.'])){
                        $args[' . $key . '] = &$' . $reference_param . ';
                    }';
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

    public static function close()
    {
        /* @var $double DoubleStub */
        foreach (self::getDouble() as $double => $double_definition) {
            $double::_doublit_close();
        }
    }

    protected static function arrayToString(array $array)
    {
        $first_run = true;
        $string = '[';
        foreach ($array as $key => $value) {
            if ($first_run) {
                $first_run = false;
            } else {
                $string .= ', ';
            }
            if (is_int($key)) {
                $string .= $key;
            } else {
                $string .= '"' . addslashes($key) . '"';
            }
            if (is_array($value)) {
                $string .= self::arrayToString($value);
            } else {
                $string .= ' => "' . addslashes($value) . '"';
            }
        }
        $string .= ']';
        return $string;
    }
}
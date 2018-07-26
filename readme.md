# Doublit - Double and test any PHP class easily in PhpUnit

[![Build Status](https://travis-ci.org/gealex/doublit.svg?branch=master)](https://travis-ci.org/gealex/doublit)

Doublit can help you to test your PHP classes by generating doubles that look like the original classes but can be manipulated and tested (sort of a copy of a class). These doubles then can then be used instead of the original classes for your test. Doublit can create doubles of any kind of class, interface or trait. 

See full documentation at [https://getdoublit.com](https://getdoublit.com)


## Installation

Add the line `"gealex/doublit": "~1.0"` in the `"require-dev"` section of your composer.json file :

    {
        "require-dev": {
            "gealex/doublit": "~1.0"
        }
    }

And run the following command :
    
    $ composer update
    
This will install the latest version of Doublit with the required PhpUnit package.

## Creating a double

A double is called a "dummy" when all the methods of the original class are overwritten to return `null`. To get a "dummy" double instance, use the `dummy_instance` method :

    // Get a double instance of type "dummy" for class "MyClass"
    $my_double = Doublit::dummy_instance(MyClass::class);

A double is called a "mock" when all the methods of the original class are overwritten to behave the same as in the original class. To get a "mock" double instance, use the `mock_instance` method :
   
    // Get a double instance of type "mock" for class "MyClass"
    $my_double = Doublit::mock_instance(MyClass::class);
   
For more details : [Read the doc on creating doubles](doc/creating_doubles.md)

## Testing a double
To test how many times a double method is called, use the `count` method :
    
    // Test that the method "myMethod" is called a least one time
    $double::_method('myMethod')->count('>=1');

To test the values of the arguments passed to a double method, use the `args` method :

    // Test that the arguments passed to method "myMethod" are "value1" and "value2"
    $double::_method('myMethod')->args(['value1', 'value2']);

To change the return value of a method, use the `stub` method. :
    
    // Make method "myMethod" return "hello"
    $my_double::_method('myMethod')->stub('hello');

For more details : [Read the doc on testing doubles](doc/testing_doubles.md)

## Configuration

You define the configuration for a specific double using the 4th argument of a Doublit instance method :</p>

    {.language-php} // Get double instance with config
    $my_double = Doublit::dummy_instance(MyClass::class, null, null, [
        'config_param_1' => 'value_1',
        'config_param_2' => 'value_1'
    ]);

Here is a list of all available config parameters :

- `allow_final_doubles` : Set this parameter to `false` to stop Doublit from trying to make doubles of final classes/methods.
- `allow_protected_methods` : Set this parameter to `false` to disallow testing protected methods.
- `allow_non_existent_classes` : Set this parameter to `false` to disallow alias doubles of non existent classes.
- `test_unexpected_methods` : Set this parameter to `true` to automatically receive an assertion error whenever an unexpected method is called.
 
For more details : [Read the doc on configuration](doc/configuration.md)

## About

### License
Doublit is licensed under the [MIT license](https://opensource.org/licenses/MIT).

### Author
Alexandre Geiswiller - [alexandre.geiswiller@gmail.com](mailto:alexandre.geiswiller@gmail.com).

For more details : [Read the doc on about](doc/about.md)
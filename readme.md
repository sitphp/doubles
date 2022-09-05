# SitPHP/Doubles

![Build Status](https://travis-ci.org/sitphp/doubles.svg?branch=master)

The "sitphp/doubles" library can help you to test your PHP classes by generating doubles that look like the original
classes but can be manipulated and tested (sort of a copy of a class). These doubles then can then be used instead of
the original classes for your test. This library can create doubles of any kind of class, interface or trait.

See full documentation : [here](https://sitphp.com/doubles/doc/intro)

## Requirements

The "sitphp/doubles" library requires at least PhpUnit 6.5 and at least PHP 7.0. It should be installed from composer
which will make sure your configuration matches requirements.
> {.note .info} Note : You can get composer here : [https://getcomposer.org](https://getcomposer.org).

## Installation

Add the line `"sitphp/doubles": "2.4.*"` in the `"require-dev"` section of your composer.json file :

```json
{
    "require-dev": {
        "sitphp/doubles": "2.4.*"
    }
}
```

And run the following command :

```bash    
composer update
```

This will install the latest version of the "sitphp/doubles" library with the required PhpUnit package.

## Creating a double

A double is called a "dummy" when all the methods of the original class are overwritten to return `null`. To get a "
dummy" double instance, use the `dummy` method :

```php
// Get a double instance of type "dummy" for class "MyClass"
$my_double = Double::dummy(MyClass::class)->getInstance();
```

A double is called a "mock" when all the methods of the original class are overwritten to behave the same as in the
original class. To get a "mock" double instance, use the `mock` method :

```php
// Get a double instance of type "mock" for class "MyClass"
$my_double = Double::mock(MyClass::class)->getInstance();
```

For more details : [Read the doc on creating doubles](doc/03_creating_doubles.md)

## Testing a double

To test how many times a double method is called, use the `count` method :

```php
// Test that the method "myMethod" is called a least one time
$double::_method('myMethod')->count('>=1');
```

To test the values of the arguments passed to a double method, use the `args` method :

```php
// Test that the arguments passed to method "myMethod" are "value1" and "value2"
$double::_method('myMethod')->args(['value1', 'value2']);
```

To change the return value of a method, use the `stub` method. :

```php
// Make method "myMethod" return "hello"
$my_double::_method('myMethod')->return('hello');
```

For more details : [Read the doc on testing doubles](doc/04_testing_doubles.md)

## Configuration

You define the configuration for a specific double using the 2nd argument of the `dummy` and `mock` methods :</p>

```php
// Get double instance with config
$my_double = Double::dummy(MyClass::class, [
    'allow_final_doubles' => true,
    'allow_non_existent_classes' => true
])->getInstance();
```

Here is a list of all available config parameters :

- `allow_final_doubles` : Set this parameter to `false` to stop trying to make doubles of final classes/methods.
- `allow_protected_methods` : Set this parameter to `false` to disallow testing protected methods.
- `allow_non_existent_classes` : Set this parameter to `false` to disallow alias doubles of non existent classes.
- `test_unexpected_methods` : Set this parameter to `true` to automatically receive an assertion error whenever an
  unexpected method is called.

For more details : [Read the doc on configuration](doc/05_configuration.md)

## About

### License

This library is licensed under the [MIT license](https://opensource.org/licenses/MIT).

### Author

Alexandre Geiswiller - [alexandre.geiswiller@gmail.com](mailto:alexandre.geiswiller@gmail.com).
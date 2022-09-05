# Testing doubles

> {.note .important} Important : For your tests to work, your PhpUnit test case classes must extend the `Doubles\TestCase` class. Read more on the [Quick start page](/doc/quick_start#testing-a-double).

## Testing method call count

To test how many times a double method is being called, use the `count` method. There are two ways :

### Using comparators

Here are a few examples of what you can do using simple comparators :

```php
// Test that the method "myMethod" is never called (0 times)
$double::_method('myMethod')->count(0);

// Test that the method "myMethod" is called exactly 2 times
$double::_method('myMethod')->count(2);

// Test that the method "myMethod" is called a least one time (more than 0 times)
$double::_method('myMethod')->count('>0');

// Test that the method "myMethod" is called a least one time (1 or more times)
$double::_method('myMethod')->count('>=1');

// Test that the method "myMethod" is called a less than 2 times
$double::_method('myMethod')->count('<2');

// Test that the method "myMethod" is called a less than 2 times (1 time or less)
$double::_method('myMethod')->count('<=1');

// Test that the method "myMethod" is called between 3 and 5 times
$double::_method('myMethod')->count('3-5');
```

### Using a callback

If you need more flexibility, you can also use a callback function to test the call count of a method. In the first
parameter of your callback function, you will be given a `$call` array containing all method calls with their arguments.
Here is and example testing that the `myCallMethod` is called 2 times through the `__call` magic method :

```php
// Test that the "__call" method has receive "myCallMethod" 2 times as a first argument
$double::_method('__call')->count(function($calls){
    $my_call_method_count = 0;
    foreach($calls as $call){
        $first_argument = $call['args'][0];
        if(first_argument == 'myCallMethod'){
            $my_call_method_count++;
        }
    }
    $this->assertEqual($my_call_method_count, 2);
});
```

## Changing method's return value

You can change the behaviour of a double method with the `stub` method.

### Return a simple value

You can replace the return value of a method with a custom value :

```php
// Make "myMethod" method return "hello"
$my_double::_method('myMethod')->return('hello');
```

### Return a custom value

You can also use the stubs methods gathered in the `Doubles\Stubs` class. They give a little bit more options. Here are
some examples on how to use them :

```php
use Doubles\Stubs;

...

// Make "myMethod" method return its second argument
$my_double::_method('myMethod')->return(Stubs::returnArgument(2));

// Make "myMethod" method return the result of the "MyClass::myMethod" method
$my_double::_method('myMethod')->return(Stubs::callback('MyClass::myMethod'));

// Make "myMethod" method throw a "MyException" exception with message "my message" (optional)
$my_double::_method('myMethod')->return(Stubs::throwException(MyException::class, 'my message'));

// Make "myMethod" method return the instance of class double
$my_double::_method('myMethod')->return(Stubs::returnSelf());

// Make "myMethod" method return "numbers" when arguments "one" and "two" are passed,
// and return "color" when arguments "blue" and "yellow" are passed
$my_double::_method('myMethod')->return(Stubs::returnValueMap([['one', 'two'],['blue', 'yellow']], ['numbers', 'colors']);
```

### Run using a callback

If you want to have full control on the behaviour of a double method, you may also define your own callback to replace
it :

```php
// Make "myMethod" return "my_return" when first argument is "my_value" and return "my_other_return" otherwise
$my_double::_method('myMethod')->return(function($arg1){
    if($arg1 == 'my_value'){
        return 'my_return';
    } else {
        return 'my_other_return';
    }
});
```

### Specific call return value

For each of the above ways of modifying a method's return value, you can specify which calls is concerned by the change:

```php
// Make "myMethod" method return "hello" the second time it is called
$my_double::_method('my_method')->return('hello', 2);

// Make "myOtherMethod" method return "hello" the second time and third time it is called
$my_double::_method('my_other_method')->return('hello', [2,3]);
```

### Make method behave like a "mock"

If you have a "mock" double and you still want some method to behave as in the original class, you should use the `mock`
method :

```php
// Make "MyMethod" method behave as in the original class
$my_dummy_double::_method('myMethod')->mock();

// Make "MyMethod" and "myOtherMethod" methods behave as in the original class
$my_dummy_double::_method(['myMethod', 'myOtherMethod'])->mock();
```

### Make method behave like a "dummy"

In the same way, if you have a "mock" double and you still want some method to behave like "dummy" and return `null`,
you should use the `dummy` method :

```php
// Make "myMethod" method return "null" at call 2 and 3
$my_mock_double::_method('myMethod')->dummy([2,3]);

// Make "MyMethod" and "myOtherMethod" methods return "null"
$my_mock_double::_method(['myMethod', 'myOtherMethod'])->dummy();
```

## Testing method's arguments

Use the `args` method to test the value of arguments passed to a double method.

### Against a simple value

You can test a method's arguments values with an array of values that the arguments should match. You can use `null` to
test that no arguments are passed to your method :

```php
// Test the first argument passed to method "myMethod" is "value1" and the second "value2"
$double::_method('myMethod')->args(['value1', 'value2']);

// Test that no arguments are passed to method "myOtherMethod"
$double::_method('myOtherMethod')->args(null);
```

### Against PhpUnit constraints

You can also use the PhpUnit constraints, gathered in the `Doubles\Constraints` class. These will give you more options
to test your arguments :

```php
use Doubles\Constraints;

// Test that the first argument passed to method "myMethod" an array and the second arguments an instance of class "MyClass"
$double::_method('myMethod')->args([Constraints::isType('array'), Constraints::isInstanceOf(MyClass::class)]);
```

Here is a list of all available constraints to test against any argument `$argument` :

- `Constraints::isTrue()` : Test that `$argument` is `true`.
- `Constraints::isFalse()` : Test that `$argument` is `false`.
- `Constraints::isNull()` : Test that `$argument` is `null`.
- `Constraints::isNotNull()` : Test that `$argument` is not `null`.
- `Constraints::isInfinite()` : Test that `$argument` is infinite.
- `Constraints::isFinite()` : Test that `$argument` is not infinite.
- `Constraints::isNan()` : Test that `$argument` is not a number.
- `Constraints::isEmpty()` : Test that `$argument` is empty.
- `Constraints::anything()` : Test that `$argument` is anything.
- `Constraints::equalTo($value, $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)` : Test
  that `$argument` is equal to `$value` (`$argument` == `$value`)
- `Constraints::identicalTo($value)` : Test that `$argument` is identical to $value (`$argument` === `$value`)
- `Constraints::isType($type)` : Test that `$argument` is of type `$type.
- `Constraints::isInstanceOf($className)` : Test that `$argument` is instance of `$className.
- `Constraints::stringStartsWith($prefix)` : Test that string `$argument` starts with `$prefix`
- `Constraints::stringContains($string, $case = true)` : Test that string `$argument` contains `$string`
- `Constraints::stringEndsWith($suffix)` : Test that string `$argument` ends with `$suffix`
- `Constraints::matchesRegularExpression($pattern)` : Test that string `$argument` matches `$pattern` regular
  expression.
- `Constraints::greaterThan($value)` : Test that numerical `$argument` is greater than `$value`.
- `Constraints::greaterThanOrEqual($value)` : Test that numerical `$argument` is greater or equal to `$value`.
- `Constraints::lessThan($value)` : Test that numerical `$argument` is less than `$value`.
- `Constraints::lessThanOrEqual($value)` : Test that numerical `$argument` is less or equal to `$value`.
- `Constraints::countOf($count)` : Test that countable `$argument` has expected `$count size`.
- `Constraints::contains($value, $checkForObjectIdentity = true, $checkForNonObjectIdentity = false)` : Test if
  array `$argument` contains `$value`.
- `Constraints::containsOnly($type)` : Test that array `$argument` contains only variables of type `$type`.
- `Constraints::containsOnlyInstancesOf($classname)` : Test that array `$argument` contains only instances of
  class `$classname`.
- `Constraints::arrayHasKey($key)` : Test that array `$argument` has the `$key`.
- `Constraints::arraySubset($subset, $strict = false)` : Test that array `$argument` contains the `$subset`.
- `Constraints::isWritable()` : Test that `$argument` is writable.
- `Constraints::isReadable()` : Test that `$argument` is readable.
- `Constraints::directoryExists()` : Test that `$argument` is an existent directory.
- `Constraints::fileExists()` : Test that `$argument` is an existent file.
- `Constraints::isJson()`: Test that `$argument` is a json.
- `Constraints::jsonMatches($expectedJson)` : Test that json `$argument` is identical to `$expectedJson`
- `Constraints::logicalAnd(...$args)` : Test that `$argument` matches all `$args` constraints
- `Constraints::logicalOr(...$args)` : Test that `$argument` matches any of the `$args` constraints
- `Constraints::logicalXor(...$args)` : Test that `$argument` matches only one of `$args` constraints
- `Constraints::logicalNot(Constraint $constraint)` : Test that `$argument` doesn't match the constraint `$constraint`
- `Constraints::callback($callback)` : Test that $callback with give argument `$argument`
  returns `true` (`$callback = function($argument){ return $argument == "test" };`)
- `Constraints::attributeEqualTo($attributeName, $value, $delta = 0.0, $maxDepth = 10, $canonicalize = false, $ignoreCase = false)` :
  Test that class or object `$argument` has attribute `$attributeName` with value `$value`.
- `Constraints::attribute(Constraint $constraint, $attributeName)` : Test classor object `$argument` has
  attribute `$attributeName` that matches given `$constraint` constraint.
- `Constraints::classHasAttribute($attributeName)` : Test that class `$argument` has attribute `$attributeName`.
- `Constraints::classHasStaticAttribute($attributeName)` : Test that class `$argument` has static
  attribute `$attributeName`.
- `Constraints::objectHasAttribute($attributeName)` : Test that object `$argument` has attribute `$attributeName`.

### Manually

If you need full control to test a method's arguments, you can run your own PhpUnit assertions using a callback
function. You will be given all the arguments passed to your method :

```php
// Test that the second argument passed to method "myMethod" is "value2" when the first argument's value is "value1"
$double::_method('myMethod')->args(function($arg1, $arg2){
    if($arg1 == 'value1'){
        $this->assertEqual($arg2, 'value2');
    }
});
```

### Specify call count test

If you don't specify on which method call you would like to test your arguments, they will be tested with the rules you
have specified every time the method is being called. You can specify on which method to test your arguments in the 2nd
argument of the `args` method  : </p>

```php 
// Test that arguments "value1" and "value2" were passed on the 3rd call of method "myMethod"
$double::_method('myMethod')->args(['value1', 'value2'], 3);

// Test that arguments "value1" and "value2" were passed on the 2nd and 3rd call of method "myMethod"
$double::_method('myMethod')->args(['value1', 'value2'], [2,3]);
``


## Putting it all together
You can chain your test assertions :
    
```php
// Test "myMethod"
$double::_method('myMethod')
    ->count('1') // make sure it is called exactly 1 time,
    ->return('my_return') // replace the return value by "my_return",
    ->args(['value1', 'value2'], 3);  // and test its arguments are "value1" and "value2" on the third call
``

## Overwriting public properties
Only public class properties can be manipulated. To modify the value of a public property, just set its value like this :

```php
// Set my_param to true
$my_double->my_property = true;
```

## Spies

Somethings you may wish to run your double code first and test it afterwards. That's what we call spy tests. For that,
you only need to write your method's tests after your double code.

To make a spy test, you would write your test in that order :

```php
// Create class double
$double = Double::dummy_instance(MyClass::class);

// Run method "myMethod"
$double->myMethod('arg1','arg2');

// Test method
$double::_method('myMethod')
    ->count(1)
    ->args(['arg1', 'arg2']);
```

Instead of writing it in that order :

```php
// Create class double
$double = Double::dummy_instance(MyClass::class);

// Test method
$double::_method('myMethod')
    ->count(1)
    ->args(['arg1', 'arg2']);

// Run method "myMethod"
$double->myMethod('arg1','arg2');
```

> {.note .info} Info : It is no possible to change a method behaviour with spies.
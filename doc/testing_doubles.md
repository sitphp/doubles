# Testing doubles
> {.note.important} Important : For your tests to work, your PhpUnit test case classes must extend the `Doublit\TestCase` class.

## Testing the count method calls
Say you have created a double and you want to test the number of times that the method "myMethod" is being called on that double. There are 3 ways you could be doing this :

### Using comparators
This is the easiest way to test how many times a double method has been called using the `count` method. Here are a few examples of what you can do using simple comparators :

    {.language-php} // Test that the method "myMethod" is never called (0 times)
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

### Using PhpUnit assertions
You can also use PhpUnit assertions to test your methods call counts. All PhpUnit assertions are gathered in the `Doublit\Constraints` class. Here are some examples of count tests using constraints :

    {.language-php}
    use Doublit\Constraints;
    ...
    
    // Test that the method "myMethod" is never called (0 times)
    $double::_method('myMethod')->count(Constraints::equalTo(0));
    
    // Test that the method "myMethod" is called exactly 2 times
    $double::_method('myMethod')->count(Constraints::equalTo(2));
    
    // Test that the method "myMethod" is called a least one time (more than 0 times)
    $double::_method('myMethod')->count(Constraints::greaterThan(0));
    
    // Test that the method "myMethod" is called a least one time (1 or more times)
    $double::_method('myMethod')->count(Constraints::greaterThanOrEqual(1));
    
    // Test that the method "myMethod" is called a less than 2 times
    $double::_method('myMethod')->count(Constraints::lessThan(2));
    
    // Test that the method "myMethod" is called a less than 2 times (1 time or less)
    $double::_method('myMethod')->count(Constraints::lessThanOrEqual(1));
    
    // Test that the method "myMethod" is called between 3 and 5 times (3 or more times and 5 or less times)
    $double::_method('myMethod')->count('[Constraints::moreThanOrEqual(3), Constraints::lessThanOrEqual(5)]);
    
### Using your own function
You can also use your own callback function to test the call count of a method. In the first parameter of your callback function, you will be given an array with all the method calls and their arguments. This could be useful, for example, to test the call count of a particular method on `__call` :

    {.language-php} // Test that the "__call" method has receive "myCallMethod" 2 times as a first argument
    $double::_method('__call')->count(function($calls){
        $my_call_method_count = 0;
        foreach($calls as $call){
            $call_arguments = $call['args'];
            
            // If the first argument of the call is "myCallMethod"
            if(call_arguments[0] == 'myCallMethod'){
                $my_call_method_count++;
            }
        }
        $this->assertEqual($my_call_method_count, 2);
    });

## Testing method's arguments
To test the arguments passed to a double method, you should use the `args` method.

### Against a simple value
To test the arguments values of a method, just pass the values that the arguments should match as an array to the `args` method. You can also give the `args` method a `null` value to test that no arguments are passed to your method :

    {.language-php} // Test the first argument passed to method "myMethod" is "value1" and the second "value2"
    $double::_method('myMethod')->args(['value1', 'value2']);
    
    // Test that no arguments are passed to method "myOtherMethod"
    $double::_method('myOtherMethod')->args(null);
   
### Against PhpUnit constraints
You can also use the PhpUnit constraints, gathered in the `Doublit\Constraints` class. They give you more options to test your arguments :

    {.language-php} // Test the first argument passed to method "myMethod" greater than "3" and that the second arguments is equal to "value2"
    $double::_method('myMethod')->args([Constraints::greaterThan(3), Constraints::equalTo('value2')]);

### Manualy
If you need full control to test a method's arguments, you can run your own PhpUnit assertions using a callback function. You will be given all the arguments passed to your method in your callback :

    {.language-php} // Test the second argument passed to method "myMethod" is "value2" when the first argument's value is "value1"
    $double::_method('myMethod')->args(function($arg1, $arg2){
        if($arg1 == 'value'){
            $this->assertEqual($arg2, 'value2');
        }
    });

### Specify call count test
If you don't specify on which method call you would like to test your arguments, they will be tested with the rules you have sepcified on every method call. If you want to specify on which method call you would like to test your arguments, you can use the 2nd argument of the `args` method  : </p>
 
    {.language-php} // Test that arguments "value1" and "value2" were passed on the 3rd call of method "myMethod"
    $double::_method('myMethod')->args(['value1', 'value2'], 3);

    // Test that arguments "value1" and "value2" were passed on the 2nd and 3rd call of method "myMethod"
    $double::_method('myMethod')->args(['value1', 'value2'], [2,3]);


## Changing a method's behaviour
You can change the behaviour of a double method with the `stub` method.

### Return a simple value
If you want replace the return value of a method with a custom value, you simply need to type the value that you would like the method to return in the `stub` method :

    {.language-php} // Make "myMethod" method return "hello"
    $my_double::_method('myMethod')->stub('hello');

### Return a custom value
Another way to modify a method's return value is to use the stubs methods gathered in the `Doublit\Stubs` class. They give a little bit more options. Here are some examples on how to use them :

    {.language-php}
    use Doublit\Stubs;
    ... 
    
    // Make "myMethod" method return its second argument
    $my_double::_method('myMethod')->stub(Stubs::returnArgument(2));
    
    // Make "myMethod" method return the result of the "MyClass::myMethod" method
    $my_double::_method('myMethod')->stub(Stubs::callback('MyClass::myMethod'));
    
    // Make "myMethod" method throw a "MyException" exception with message "my message" (optional)
    $my_double::_method('myMethod')->stub(Stubs::throwException(MyException::class, 'my message'));
    
    // Make "myMethod" method return the instance of class double
    $my_double::_method('myMethod')->stub(Stubs::returnSelf());
    
    // Make "myMethod" method return "numbers" when arguments "one" and "two" are passed,
    // and return "color" when arguments "blue" and "yellow" are passed
    $my_double::_method('myMethod')->stub(Stubs::returnValueMap([['one', 'two'],['blue', 'yellow']], ['numbers', 'colors']);

### Run a callback
If you want to have full control on the behaviour of a double method, you may also define your own function to replace it :

    {.language-php} // Make "myMethod" return "my_return" when first argument is "my_value" and return "my_other_return" otherwise
    $my_double::_method('myMethod')->stub(function($arg1){
        if($arg1 == 'my_value'){
            return 'my_return';
        } else {
            return 'my_other_return';
        }
    });
   
### Make method behave like a "mock"
If you have a "mock" double and you still want some method to behave as in the original class, you should use the `mock` method :

    {.language-php} // Make "MyMethod" method behave as in the original class
    $my_dummy_double::_method('myMethod')->mock();
    
    // Make "MyMethod" and "myOtherMethod" methods behave as in the original class
    $my_dummy_double::_method(['myMethod', 'myOtherMethod'])->mock();
    
### Make method behave like a "dummy"
In the same way, if you have a "mock" double and you still want some method to behave like "dummy" and return `null`, you should use the `dummy` method :

    {.language-php} // Make "myMethod" method return "null"
    $my_mock_double::_method('myMethod')->dummy();
    
    // Make "MyMethod" and "myOtherMethod" methods return "null"
    $my_mock_double::_method(['myMethod', 'myOtherMethod'])->dummy();

### Specify call count overwriting
For each of the above ways of changing a method's behaviour, you can specify which calls is concerned by the change:

    {.language-php} // Make "myMethod" method return "hello" the second time it is called
    $my_double::_method('my_method')->stub('hello', 2);

    // Make "myOtherMethod" method return "hello" the second time and third time it is called
    $my_double::_method('my_other_method')->stub('hello', [2,3]);

## Putting it all together
You can chain your test assertions :
    
    {.language-php} 
    // Test "myMethod"
    $double::_method('myMethod')
    ->count('1') // make sure it is called exactly 1 time,
    ->stub('my_return') // replace the return value by "my_return",
    ->args(['value1', 'value2'], 3);  // and test its arguments are "value1" and "value2" on the third call

## Overwriting public properties
Only public class properties can be manipulated. To modify the value of a public property, just set its value like this :

    {.language-php} // Set my_param to true
    $my_double->my_property = true;

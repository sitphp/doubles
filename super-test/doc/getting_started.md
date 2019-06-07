# Getting started

## Creating a compatible test case
In order to run PhpUnit test cases with Doublit, your test class should extend the `Doublit\TestCase` class. This class extends the  `PHPUnit\Framework\TestCase` class so you can still use the PhpUnit methods normally. Here is what a test class should look like :

    {.language-php} 
    
    use \Doublit\TestCase;
    
    class MyTestClass extends TestCase {
    
        function testMyFirstTest(){
            // your test here ...
        }
        

    }
    
This will make sure the `Doublit\Doublit::close()` method is executed after each test. The next step is to create class double of the class we want to test.


## Creating a double

### Creating a "dummy" double
A double is called a "dummy" when all the methods of the original class are overwritten to return `null`. If you are not sure what doubles of type "dummy" are, you can get more details on the [introduction page](/doc/intro). To get a "dummy" double, use the `dummy` method class :

    {.language-php} 
   
    use \Doublit\Doublit;
    use \Doublit\TestCase;
    
    class MyTestClass extends TestCase {
        
        function testMyFirstTest(){
            // Get a double instance of type "dummy" for class "Foo"
            $double = Doublit::dummy(Foo::class)->getInstance();
        }
        
    }

### Creating a "mock" double
A double is called a "mock" when all the methods of the original class are overwritten to behave the same as in the original class. If you are not sure what doubles of type "mock" are, you can get more details on the [introduction page](/doc/intro). To get a "mock" double instance, use the `mock` method :

    {.language-php} 
    use \Doublit\Doublit;
    use \Doublit\TestCase;
    
    class MyTestClass extends TestCase {
        
        function testMyFirstTest(){
            // Get a double instance of type "mock" for class "Foo"
            $double = Doublit::mock(Foo::class)->getInstance();
        }
        
    }

For more details : [Read the doc on creating doubles](/doc/creating_doubles)


## Testing a double

First use the `Doublit::_method` method to tell Doublit which method you would like to test. Then use the following methods depending on what you want to test : 

- To test how many times a double method is called : use the `count` method.
- To test the values of the arguments passed to a double method : use the `args` method.
- To change a method's behaviour : use the `stub` method.

Here is a full working example :

    {.language-php} 
    use \Doublit\Doublit;
    use \Doublit\TestCase;

    class MyTestClass extends TestCase {

        function testMyFirstTest(){

            /* Get a double instance of type "dummy" for class "Foo" */
            $double = Doublit::dummy(Foo::class)->getInstance();

            /* Set double test expectations for method "myMethod" */
            $double::_method('myMethod')
                ->count('>=1') // Test that the method is called a least one time
                ->args(['value1', 'value2']); // Test that the given arguments are "value1" and "value2"
                ->stub('hello'); // Make the method return "hello"

            /* We can now test the "ClassToTest" class injecting our "Foo" class double */
            $class_to_test = new ClassToTest($double);
            $result = $class_to_test->methodToTest();
            
            /* Test the result with PhpUnit */
            $this->assertEquals('result', $result);

        }
    }

For more details : [Read the doc on testing doubles](/doc/testing_doubles)